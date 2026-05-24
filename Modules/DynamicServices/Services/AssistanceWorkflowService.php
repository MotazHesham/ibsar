<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use App\Models\DonationItem;
use App\Models\UserAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Workflows\AssistanceWorkflowHandler;

class AssistanceWorkflowService
{
    public function getViewData(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        return [
            'isFinancial' => $service->service_type === AssistanceWorkflowHandler::SUBTYPE_FINANCIAL,
            'researcherReview' => $workflowData['researcher'] ?? [],
            'projectsReview' => $workflowData['projects'] ?? [],
            'financialReview' => $workflowData['financial'] ?? [],
            'allocation' => $workflowData['allocation'] ?? [],
            'pickup' => $workflowData['pickup'] ?? [],
            'otp' => $workflowData['otp'] ?? [],
            'incompleteDocs' => $workflowData['incomplete_docs'] ?? [],
            'satisfaction' => $workflowData['satisfaction'] ?? [],
            'availableStockItems' => $this->getAvailableStockItems(),
            'allocatedAmount' => $workflowData['financial']['amount'] ?? null,
        ];
    }

    public function getAvailableStockItems()
    {
        return DonationItem::query()
            ->where('quantity', '>', 0)
            ->orderBy('item_name')
            ->pluck('item_name', 'id');
    }

    public function validateAndExtract(string $step, string $action, array $data, DynamicService $service): array
    {
        return match (true) {
            $step === AssistanceWorkflowHandler::STEP_FIRST_APPROVAL && $action === AssistanceWorkflowHandler::ACTION_APPROVE_RESEARCHER
                => $this->validateResearcherApproval($data, $service),
            $step === AssistanceWorkflowHandler::STEP_FIRST_APPROVAL && $action === AssistanceWorkflowHandler::ACTION_REQUEST_INCOMPLETE
                => $this->validateIncompleteDocsRequest($data),
            $step === AssistanceWorkflowHandler::STEP_SECOND_APPROVAL && $action === AssistanceWorkflowHandler::ACTION_APPROVE_PROJECTS
                => $this->validateProjectsApproval($data, $service),
            $step === AssistanceWorkflowHandler::STEP_THIRD_APPROVAL && $action === AssistanceWorkflowHandler::ACTION_DISBURSE_FINANCE
                => $this->validateFinanceDisbursement($data),
            $step === AssistanceWorkflowHandler::STEP_RECEIVE_ORDER && $action === AssistanceWorkflowHandler::ACTION_VERIFY_OTP
                => $this->validateOtpVerification($data),
            default => $data,
        };
    }

    protected function validateResearcherApproval(array $data, DynamicService $service): array
    {
        if ($service->service_type === AssistanceWorkflowHandler::SUBTYPE_FINANCIAL) {
            $validated = Validator::make($data, [
                'researcher_notes' => 'nullable|string|max:2000',
                'allocated_amount' => 'required|numeric|min:0',
            ], [
                'allocated_amount.required' => 'يرجى تحديد المبلغ المرصود',
            ])->validate();

            return [
                'researcher' => [
                    'approved' => true,
                    'notes' => $validated['researcher_notes'] ?? null,
                    'allocated_amount' => (float) $validated['allocated_amount'],
                    'approved_at' => now()->toDateTimeString(),
                ],
            ];
        }

        $validated = Validator::make($data, [
            'donation_item_id' => 'required|exists:donation_items,id',
            'allocated_quantity' => 'required|numeric|min:0.01',
            'researcher_notes' => 'nullable|string|max:2000',
            'stock_available' => 'required|in:yes,no',
        ], [
            'donation_item_id.required' => 'يرجى اختيار الصنف',
            'allocated_quantity.required' => 'يرجى تحديد الكمية',
            'stock_available.required' => 'يرجى تحديد توفر المخزون',
        ])->validate();

        if ($validated['stock_available'] === 'no') {
            throw ValidationException::withMessages([
                'stock_available' => 'الكمية غير متوفرة في المخزون',
            ]);
        }

        $item = DonationItem::findOrFail($validated['donation_item_id']);
        if ((float) $validated['allocated_quantity'] > (float) $item->quantity) {
            throw ValidationException::withMessages([
                'allocated_quantity' => 'الكمية المطلوبة أكبر من المتاح في المخزون (' . $item->quantity . ')',
            ]);
        }

        return [
            'researcher' => [
                'approved' => true,
                'notes' => $validated['researcher_notes'] ?? null,
                'approved_at' => now()->toDateTimeString(),
            ],
            'allocation' => [
                'donation_item_id' => (int) $item->id,
                'item_name' => $item->item_name,
                'quantity' => (float) $validated['allocated_quantity'],
                'reserved_at' => now()->toDateTimeString(),
            ],
        ];
    }

    protected function validateIncompleteDocsRequest(array $data): array
    {
        $validated = Validator::make($data, [
            'incomplete_message' => 'required|string|max:2000',
        ], [
            'incomplete_message.required' => 'يرجى توضيح الأمور الناقصة',
        ])->validate();

        return [
            'incomplete_docs' => [
                'message' => $validated['incomplete_message'],
                'requested_at' => now()->toDateTimeString(),
                'completed_at' => null,
            ],
            'status' => 'incomplete',
        ];
    }

    protected function validateProjectsApproval(array $data, DynamicService $service): array
    {
        if ($service->service_type === AssistanceWorkflowHandler::SUBTYPE_FINANCIAL) {
            $validated = Validator::make($data, [
                'approved_amount' => 'required|numeric|min:0',
                'projects_notes' => 'nullable|string|max:2000',
            ])->validate();

            return [
                'projects' => [
                    'approved' => true,
                    'notes' => $validated['projects_notes'] ?? null,
                    'approved_at' => now()->toDateTimeString(),
                ],
                'financial' => [
                    'amount' => (float) $validated['approved_amount'],
                    'approved' => true,
                    'approved_at' => now()->toDateTimeString(),
                ],
            ];
        }

        $validated = Validator::make($data, [
            'requires_training' => 'required|in:yes,no',
            'training_type' => 'nullable|in:individual,group',
            'projects_notes' => 'nullable|string|max:2000',
            'pickup_deadline_days' => 'nullable|integer|min:1|max:30',
        ])->validate();

        if ($validated['requires_training'] === 'yes' && empty($validated['training_type'])) {
            throw ValidationException::withMessages([
                'training_type' => 'يرجى تحديد نوع التدريب',
            ]);
        }

        return [
            'projects' => [
                'approved' => true,
                'requires_training' => $validated['requires_training'] === 'yes',
                'training_type' => $validated['training_type'] ?? null,
                'notes' => $validated['projects_notes'] ?? null,
                'pickup_deadline_days' => (int) ($validated['pickup_deadline_days'] ?? 14),
                'approved_at' => now()->toDateTimeString(),
            ],
            'pickup' => [
                'deadline_at' => now()->addDays((int) ($validated['pickup_deadline_days'] ?? 14))->toDateTimeString(),
                'reminder_due_at' => now()->addDays(7)->toDateTimeString(),
            ],
            'warehouse_request' => [
                'requested_at' => now()->toDateTimeString(),
                'status' => 'pending',
            ],
        ];
    }

    protected function validateFinanceDisbursement(array $data): array
    {
        $validated = Validator::make($data, [
            'disbursement_reference' => 'nullable|string|max:255',
            'finance_notes' => 'nullable|string|max:2000',
        ])->validate();

        return [
            'financial' => [
                'disbursed' => true,
                'disbursement_reference' => $validated['disbursement_reference'] ?? null,
                'notes' => $validated['finance_notes'] ?? null,
                'disbursed_at' => now()->toDateTimeString(),
            ],
        ];
    }

    protected function validateOtpVerification(array $data): array
    {
        return Validator::make($data, [
            'otp_code' => 'required|string|size:6',
        ], [
            'otp_code.required' => 'يرجى إدخال رمز OTP',
        ])->validate();
    }

    public function mergeWorkflowData(DynamicServiceOrder $dynamicServiceOrder, array $payload): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        foreach ($payload as $key => $value) {
            if (in_array($key, ['researcher', 'projects', 'financial', 'allocation', 'pickup', 'otp', 'incomplete_docs', 'satisfaction', 'warehouse_request'], true)
                && isset($workflowData[$key]) && is_array($workflowData[$key]) && is_array($value)) {
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
            'otp' => [
                'code' => $code,
                'sent_at' => now()->toDateTimeString(),
                'verified_at' => null,
            ],
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
        if (! $item) {
            return;
        }

        $item->update([
            'quantity' => max(0, (float) $item->quantity - $quantity),
        ]);

        $this->mergeWorkflowData($dynamicServiceOrder, [
            'allocation' => array_merge($allocation, [
                'deducted_at' => now()->toDateTimeString(),
            ]),
        ]);
    }

    public function submitBeneficiaryPickupSchedule(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
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

        $this->appendBeneficiaryHistory($dynamicServiceOrder, AssistanceWorkflowHandler::STEP_RECEIVE_ORDER, 'beneficiary_pickup_schedule', [
            'date' => $validated['pickup_date'],
            'time' => $validated['pickup_time'],
        ]);

        $this->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تأكيد موعد الاستلام: ' . $validated['pickup_date'] . ' الساعة ' . $validated['pickup_time'] .
                '. رمز الاستلام (OTP): ' . $otp . ' — يرجى إظهاره عند قسم المخزون.'
        );
    }

    public function submitBeneficiaryIncompleteDocs(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
        $validated = Validator::make($data, [
            'completion_notes' => 'required|string|max:2000',
        ])->validate();

        $this->mergeWorkflowData($dynamicServiceOrder, [
            'incomplete_docs' => array_merge($dynamicServiceOrder->workflow_data['incomplete_docs'] ?? [], [
                'completion_notes' => $validated['completion_notes'],
                'completed_at' => now()->toDateTimeString(),
            ]),
            'status' => 'pending_researcher_review',
        ]);

        $this->appendBeneficiaryHistory($dynamicServiceOrder, AssistanceWorkflowHandler::STEP_FIRST_APPROVAL, 'beneficiary_docs_completed', [
            'notes' => $validated['completion_notes'],
        ]);

        $this->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم استلام تحديثاتكم. سيتم إشعار الباحث الاجتماعي لإعادة دراسة الطلب.'
        );
    }

    public function submitBeneficiarySatisfaction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
        $validated = Validator::make($data, [
            'satisfaction_rating' => 'required|integer|min:1|max:5',
            'satisfaction_comment' => 'nullable|string|max:2000',
        ])->validate();

        if (! empty($dynamicServiceOrder->workflow_data['satisfaction']['completed_at'])) {
            throw ValidationException::withMessages([
                'satisfaction' => 'تم إكمال استبيان الرضا مسبقاً.',
            ]);
        }

        $this->mergeWorkflowData($dynamicServiceOrder, [
            'satisfaction' => [
                'rating' => (int) $validated['satisfaction_rating'],
                'comment' => $validated['satisfaction_comment'] ?? null,
                'completed_at' => now()->toDateTimeString(),
            ],
        ]);

        $beneficiaryOrder->update(['done' => 1]);

        $this->appendBeneficiaryHistory($dynamicServiceOrder, AssistanceWorkflowHandler::STEP_COMPLETED, 'beneficiary_satisfaction', [
            'rating' => (int) $validated['satisfaction_rating'],
            'comment' => $validated['satisfaction_comment'] ?? null,
        ]);

        $this->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'شكراً لإكمال استبيان الرضا عن برنامج المساعدات.'
        );
    }

    protected function appendBeneficiaryHistory(DynamicServiceOrder $dynamicServiceOrder, string $step, string $action, array $meta = []): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $history = $workflowData['history'] ?? [];
        $history[] = array_merge([
            'step' => $step,
            'action' => $action,
            'at' => now()->toDateTimeString(),
            'by' => auth()->id(),
        ], $meta);
        $workflowData['history'] = $history;
        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
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
}
