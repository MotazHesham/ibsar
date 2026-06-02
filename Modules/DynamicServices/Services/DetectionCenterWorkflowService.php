<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use App\Models\DonationItem;
use App\Models\UserAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Workflows\DetectionCenterWorkflowHandler;

class DetectionCenterWorkflowService
{
    public const EXAM_TYPES = [
        'reception' => 'الاستقبال',
        'social_research' => 'البحث الاجتماعي',
        'clinic' => 'العيادة',
    ];

    public const DOCTOR_OUTCOMES = [
        'external_clinic' => 'كشوفات إضافية في عيادة خارجية',
        'low_vision_clinic' => 'كشف متخصص عيادة ضعف الإبصار',
        'no_further_action' => 'لا يحتاج لإجراء إضافي',
    ];

    public const VISUAL_AIDS = [
        'white_cane' => 'عصا بيضاء',
        'magnifier' => 'مكبر',
        'electronic_magnifier' => 'مكبر إلكتروني',
        'glasses' => 'نظارات',
    ];

    public function getViewData(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        return [
            'examAppointment' => $workflowData['exam_appointment'] ?? [],
            'doctorEvaluation' => $workflowData['doctor_evaluation'] ?? [],
            'contribution' => $workflowData['contribution'] ?? [],
            'financial' => $workflowData['financial'] ?? [],
            'allocation' => $workflowData['allocation'] ?? [],
            'pickup' => $workflowData['pickup'] ?? [],
            'otp' => $workflowData['otp'] ?? [],
            'satisfaction' => $workflowData['satisfaction'] ?? [],
            'examTypes' => self::EXAM_TYPES,
            'doctorOutcomes' => self::DOCTOR_OUTCOMES,
            'visualAids' => self::VISUAL_AIDS,
            'availableStockItems' => DonationItem::where('quantity', '>', 0)->orderBy('item_name')->pluck('item_name', 'id'),
            'beneficiaryDob' => $beneficiaryOrder->beneficiary?->dob,
        ];
    }

    public function validateAndExtract(string $step, string $action, array $data): array
    {
        return match (true) {
            $step === DetectionCenterWorkflowHandler::STEP_INITIAL_APPROVAL && $action === DetectionCenterWorkflowHandler::ACTION_SCHEDULE_EXAM
                => $this->validateExamSchedule($data),
            $step === DetectionCenterWorkflowHandler::STEP_DOCTOR_EVALUATION && $action === DetectionCenterWorkflowHandler::ACTION_SUBMIT_EVALUATION
                => $this->validateDoctorEvaluation($data),
            $step === DetectionCenterWorkflowHandler::STEP_FINANCIAL_APPROVAL && $action === DetectionCenterWorkflowHandler::ACTION_APPROVE_FINANCIAL
                => $this->validateFinancialApproval($data),
            $step === DetectionCenterWorkflowHandler::STEP_RECEIVE_ORDER && $action === DetectionCenterWorkflowHandler::ACTION_VERIFY_OTP
                => Validator::make($data, ['otp_code' => 'required|string|size:6'])->validate(),
            default => $data,
        };
    }

    protected function validateExamSchedule(array $data): array
    {
        $validated = Validator::make($data, [
            'exam_date' => 'required|date_format:' . config('panel.date_format'),
            'exam_time' => 'required|date_format:H:i',
            'exam_types' => 'required|array|min:1',
            'exam_types.*' => 'in:' . implode(',', array_keys(self::EXAM_TYPES)),
        ])->validate();

        return [
            'exam_appointment' => [
                'date' => $validated['exam_date'],
                'time' => $validated['exam_time'],
                'types' => $validated['exam_types'],
                'sent_at' => now()->toDateTimeString(),
            ],
        ];
    }

    protected function validateDoctorEvaluation(array $data): array
    {
        $validated = Validator::make($data, [
            'outcome' => 'required|in:' . implode(',', array_keys(self::DOCTOR_OUTCOMES)),
            'visual_aids' => 'nullable|array',
            'visual_aids.*' => 'string',
            'evaluation_notes' => 'nullable|string|max:3000',
            'contribution_amount' => 'nullable|numeric|min:0',
        ])->validate();

        if ($validated['outcome'] === 'low_vision_clinic' && empty($validated['contribution_amount'])) {
            throw ValidationException::withMessages([
                'contribution_amount' => 'يرجى تحديد قيمة المساهمة',
            ]);
        }

        return ['doctor_evaluation' => array_merge($validated, ['submitted_at' => now()->toDateTimeString()])];
    }

    protected function validateFinancialApproval(array $data): array
    {
        return ['financial' => [
            'approved' => true,
            'approved_at' => now()->toDateTimeString(),
            'notes' => $data['finance_notes'] ?? null,
        ]];
    }

    public function mergeWorkflowData(DynamicServiceOrder $dynamicServiceOrder, array $payload): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        foreach ($payload as $key => $value) {
            if (isset($workflowData[$key]) && is_array($workflowData[$key]) && is_array($value)) {
                $workflowData[$key] = array_merge($workflowData[$key], $value);
            } else {
                $workflowData[$key] = $value;
            }
        }
        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
    }

    public function generateOtp(DynamicServiceOrder $dynamicServiceOrder): string
    {
        $code = (string) random_int(100000, 999999);
        $this->mergeWorkflowData($dynamicServiceOrder, [
            'otp' => ['code' => $code, 'sent_at' => now()->toDateTimeString(), 'verified_at' => null],
        ]);

        return $code;
    }

    public function verifyOtp(DynamicServiceOrder $dynamicServiceOrder, string $code): bool
    {
        $stored = $dynamicServiceOrder->workflow_data['otp']['code'] ?? null;

        return $stored && hash_equals((string) $stored, (string) $code);
    }

    public function deductInventory(DynamicServiceOrder $dynamicServiceOrder): void
    {
        $allocation = $dynamicServiceOrder->workflow_data['allocation'] ?? [];
        $itemId = $allocation['donation_item_id'] ?? null;
        $quantity = (float) ($allocation['quantity'] ?? 0);
        if (! $itemId || $quantity <= 0) {
            return;
        }
        $item = DonationItem::find($itemId);
        if ($item) {
            $item->update(['quantity' => max(0, (float) $item->quantity - $quantity)]);
        }
    }

    public function notifyBeneficiaryUserAlert(BeneficiaryOrder $beneficiaryOrder, string $text, ?string $link = null): void
    {
        $userId = $beneficiaryOrder->beneficiary?->user_id;
        if (! $userId) {
            return;
        }
        $alert = UserAlert::create([
            'alert_text' => $text,
            'alert_link' => $link ?? route('beneficiary.beneficiary-orders.show', $beneficiaryOrder),
            'user_type' => 'beneficiary',
        ]);
        $alert->users()->sync([$userId]);
    }

    public function submitBeneficiaryPickupSchedule(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $validated = Validator::make($data, [
            'pickup_date' => 'required|date_format:' . config('panel.date_format'),
            'pickup_time' => 'required|date_format:H:i',
        ])->validate();

        $otp = $this->generateOtp($dynamicServiceOrder);
        $this->mergeWorkflowData($dynamicServiceOrder, [
            'pickup' => array_merge($dynamicServiceOrder->workflow_data['pickup'] ?? [], [
                'date' => $validated['pickup_date'],
                'time' => $validated['pickup_time'],
                'confirmed_at' => now()->toDateTimeString(),
            ]),
        ]);
        $this->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'موعد الاستلام: ' . $validated['pickup_date'] . ' — OTP: ' . $otp
        );
    }

    public function submitBeneficiarySatisfaction(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $validated = Validator::make($data, [
            'satisfaction_rating' => 'required|integer|min:1|max:5',
            'satisfaction_comment' => 'nullable|string|max:2000',
        ])->validate();

        $this->mergeWorkflowData($dynamicServiceOrder, [
            'satisfaction' => [
                'rating' => (int) $validated['satisfaction_rating'],
                'comment' => $validated['satisfaction_comment'] ?? null,
                'completed_at' => now()->toDateTimeString(),
            ],
        ]);
        $beneficiaryOrder->update(['done' => 1]);
        $this->notifyBeneficiaryUserAlert($beneficiaryOrder, 'شكراً لإكمال استبيان الرضا.');
    }

    public function submitBeneficiaryReceipt(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
        $validated = Validator::make($data, [
            'receipt_notes' => 'nullable|string|max:2000',
        ])->validate();

        $dynamicServiceOrder->refresh();
        $financial = $dynamicServiceOrder->workflow_data['financial'] ?? [];

        $this->mergeWorkflowData($dynamicServiceOrder, [
            'financial' => array_merge($financial, [
                'receipt_submitted_at' => now()->toDateTimeString(),
                'receipt_notes' => $validated['receipt_notes'] ?? null,
            ]),
        ]);

        $this->appendReceiptHistory($dynamicServiceOrder, $validated['receipt_notes'] ?? null);

        $this->notifyBeneficiaryUserAlert($beneficiaryOrder, 'تم استلام إيصال السداد. جاري مراجعته من المالية.');
    }

    protected function appendReceiptHistory(DynamicServiceOrder $dynamicServiceOrder, ?string $notes): void
    {
        $dynamicServiceOrder->refresh();
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $history = $workflowData['history'] ?? [];
        $history[] = [
            'step' => DetectionCenterWorkflowHandler::STEP_FINANCIAL_APPROVAL,
            'action' => 'beneficiary_receipt',
            'at' => now()->toDateTimeString(),
            'by' => auth()->id(),
            'notes' => $notes,
        ];
        $workflowData['history'] = $history;
        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
    }
}
