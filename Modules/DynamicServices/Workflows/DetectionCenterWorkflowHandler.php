<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Services\DetectionCenterWorkflowService;

class DetectionCenterWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_ATTENDANCE = 'attendance';
    public const STEP_DOCTOR_EVALUATION = 'second_approval';
    public const STEP_FINANCIAL_APPROVAL = 'financial_approval';
    public const STEP_RECEIVE_ORDER = 'receive_order';

    public const ACTION_SCHEDULE_EXAM = 'schedule_exam';
    public const ACTION_SUBMIT_EVALUATION = 'submit_evaluation';
    public const ACTION_APPROVE_FINANCIAL = 'approve_financial';
    public const ACTION_REJECT_FINANCIAL = 'reject_financial';
    public const ACTION_VERIFY_OTP = 'verify_otp';
    public const ACTION_ALLOCATE_DEVICE = 'allocate_device';

    public function __construct(
        protected DetectionCenterWorkflowService $detectionService
    ) {
    }

    public function category(): string
    {
        return DynamicService::CATEGORY_DETECTION_CENTER;
    }

    public function viewName(): string
    {
        return 'dynamicservices::workflows.detection-center.edit-status';
    }

    public function steps(DynamicService $service): array
    {
        return [
            self::STEP_INITIAL_APPROVAL => 'اعتماد الاستقبال',
            self::STEP_ATTENDANCE => 'الحضور',
            self::STEP_DOCTOR_EVALUATION => 'تقييم الدكتور',
            self::STEP_FINANCIAL_APPROVAL => 'اعتماد المالية',
            self::STEP_RECEIVE_ORDER => 'استلام الطلب',
            self::STEP_COMPLETED => 'مكتمل',
        ];
    }

    public function initialStep(DynamicService $service): string
    {
        return self::STEP_INITIAL_APPROVAL;
    }

    public function availableActions(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        if ($this->isCompleted($dynamicServiceOrder) || $this->isRejected($dynamicServiceOrder)) {
            return [];
        }

        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $outcome = $workflowData['doctor_evaluation']['outcome'] ?? null;

        return match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => [
                ['key' => self::ACTION_SCHEDULE_EXAM, 'label' => 'حفظ وإرسال موعد الكشف', 'type' => 'primary', 'form_submit' => true],
                ['key' => self::ACTION_REJECT, 'label' => 'لا يعتمد', 'type' => 'danger', 'requires_reason' => true],
            ],
            self::STEP_ATTENDANCE => array_merge($this->attendanceActions(), [[
                'key' => 'reschedule',
                'label' => 'إعادة جدولة',
                'type' => 'secondary',
            ]]),
            self::STEP_DOCTOR_EVALUATION => [[
                'key' => self::ACTION_SUBMIT_EVALUATION,
                'label' => 'حفظ تقييم الدكتور',
                'type' => 'primary',
                'form_submit' => true,
            ]],
            self::STEP_FINANCIAL_APPROVAL => [
                ['key' => self::ACTION_APPROVE_FINANCIAL, 'label' => 'يعتمد (المالية)', 'type' => 'success', 'form_submit' => true],
                ['key' => self::ACTION_REJECT_FINANCIAL, 'label' => 'لا يعتمد', 'type' => 'danger', 'requires_reason' => true],
            ],
            self::STEP_RECEIVE_ORDER => $this->receiveActions($workflowData, $outcome),
            default => [],
        };
    }

    protected function receiveActions(array $workflowData, ?string $outcome): array
    {
        if ($outcome !== 'low_vision_clinic') {
            return [];
        }
        $actions = [];
        if (empty($workflowData['allocation']['item_name'] ?? null)) {
            $actions[] = ['key' => self::ACTION_ALLOCATE_DEVICE, 'label' => 'تخصيص المعين', 'type' => 'primary', 'form_submit' => true];
        }
        if (! empty($workflowData['pickup']['confirmed_at'])) {
            $actions[] = ['key' => self::ACTION_VERIFY_OTP, 'label' => 'تأكيد الاستلام (OTP)', 'type' => 'success', 'form_submit' => true];
        }

        return $actions;
    }

    public function processAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        array $data = []
    ): void {
        $step = $dynamicServiceOrder->workflow_step;

        if ($action === self::ACTION_REJECT || $action === self::ACTION_REJECT_FINANCIAL) {
            $this->reject($beneficiaryOrder, $dynamicServiceOrder, $data['reason'] ?? null);
            $this->detectionService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'نعتذر، لم يتم قبول طلب الكشف. ' . ($data['reason'] ?? ''));

            return;
        }

        if ($step === self::STEP_ATTENDANCE && $action === self::ACTION_MARK_NOT_ATTENDED) {
            $this->setWorkflowMeta($dynamicServiceOrder, 'attendance', 'not_attended');
            $this->moveToStep($dynamicServiceOrder, self::STEP_INITIAL_APPROVAL, 0);
            $this->detectionService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'لم يتم تسجيل حضوركم. سيتم إعادة جدولة الموعد.');
            $this->appendHistory($dynamicServiceOrder, self::STEP_ATTENDANCE, self::ACTION_MARK_NOT_ATTENDED);

            return;
        }

        if ($step === self::STEP_ATTENDANCE && $action === 'reschedule') {
            $this->moveToStep($dynamicServiceOrder, self::STEP_INITIAL_APPROVAL, 0);

            return;
        }

        $validated = $this->detectionService->validateAndExtract($step, $action, $data);

        match (true) {
            $step === self::STEP_INITIAL_APPROVAL && $action === self::ACTION_SCHEDULE_EXAM
                => $this->handleScheduleExam($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_ATTENDANCE && $action === self::ACTION_MARK_ATTENDED
                => $this->handleMarkAttended($beneficiaryOrder, $dynamicServiceOrder),
            $step === self::STEP_DOCTOR_EVALUATION && $action === self::ACTION_SUBMIT_EVALUATION
                => $this->handleDoctorEvaluation($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_FINANCIAL_APPROVAL && $action === self::ACTION_APPROVE_FINANCIAL
                => $this->handleFinancialApproval($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_RECEIVE_ORDER && $action === self::ACTION_ALLOCATE_DEVICE
                => $this->handleAllocateDevice($beneficiaryOrder, $dynamicServiceOrder, $data),
            $step === self::STEP_RECEIVE_ORDER && $action === self::ACTION_VERIFY_OTP
                => $this->handleOtp($beneficiaryOrder, $dynamicServiceOrder, $data),
            default => null,
        };
    }

    protected function handleScheduleExam(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->detectionService->mergeWorkflowData($dynamicServiceOrder, $validated);
        if ($beneficiaryOrder->accept_status !== 'yes') {
            $beneficiaryOrder->update(['accept_status' => 'yes']);
        }
        $this->moveToStep($dynamicServiceOrder, self::STEP_ATTENDANCE, 1);
        $appt = $validated['exam_appointment'] ?? [];
        $this->detectionService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'موعد الكشف: ' . ($appt['date'] ?? '') . ' الساعة ' . ($appt['time'] ?? '')
        );
        $this->appendHistory($dynamicServiceOrder, self::STEP_INITIAL_APPROVAL, self::ACTION_SCHEDULE_EXAM, $appt);
    }

    protected function handleMarkAttended(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->setWorkflowMeta($dynamicServiceOrder, 'attendance', 'attended');
        $this->moveToStep($dynamicServiceOrder, self::STEP_DOCTOR_EVALUATION, 2);
        $this->appendHistory($dynamicServiceOrder, self::STEP_ATTENDANCE, self::ACTION_MARK_ATTENDED);
    }

    protected function handleDoctorEvaluation(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $evaluation = $validated['doctor_evaluation'] ?? [];
        $this->detectionService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $outcome = $evaluation['outcome'] ?? '';

        match ($outcome) {
            'external_clinic' => $this->handleOutcomeExternal($beneficiaryOrder, $dynamicServiceOrder),
            'low_vision_clinic' => $this->handleOutcomeLowVision($beneficiaryOrder, $dynamicServiceOrder, $evaluation),
            'no_further_action' => $this->handleOutcomeNone($beneficiaryOrder, $dynamicServiceOrder),
            default => null,
        };
        $this->appendHistory($dynamicServiceOrder, self::STEP_DOCTOR_EVALUATION, self::ACTION_SUBMIT_EVALUATION, $evaluation);
    }

    protected function handleOutcomeExternal(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);
        $this->detectionService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم حفظ تقرير الكشف. يمكنكم إنشاء طلب كشف طبي عبر خدمة العمليات الجراحية.'
        );
    }

    protected function handleOutcomeLowVision(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $evaluation): void
    {
        $this->detectionService->mergeWorkflowData($dynamicServiceOrder, [
            'contribution' => [
                'amount' => (float) ($evaluation['contribution_amount'] ?? 0),
                'submitted_at' => now()->toDateTimeString(),
            ],
        ]);
        $this->moveToStep($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, 3);
        $this->detectionService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم حفظ التقييم. يرجى السداد ورفع الإيصال من صفحة الطلب.'
        );
    }

    protected function handleOutcomeNone(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);
        $this->detectionService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم حفظ تقرير الكشف. يرجى إكمال استبيان الرضا من صفحة الطلب.'
        );
    }

    protected function handleFinancialApproval(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->detectionService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_RECEIVE_ORDER, 4);
        $this->detectionService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم اعتماد المساهمة. سيتوفر الطلب خلال 30 يوماً. يرجى تأكيد موعد الاستلام من صفحة الطلب.'
        );
        $this->appendHistory($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, self::ACTION_APPROVE_FINANCIAL);
    }

    protected function handleAllocateDevice(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $itemId = $data['donation_item_id'] ?? null;
        $item = $itemId ? \App\Models\DonationItem::find($itemId) : null;
        $this->detectionService->mergeWorkflowData($dynamicServiceOrder, [
            'allocation' => [
                'donation_item_id' => $item?->id,
                'item_name' => $item?->item_name ?? $data['item_name'] ?? '',
                'quantity' => 1,
                'allocated_at' => now()->toDateTimeString(),
            ],
        ]);
        $this->detectionService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'تم تخصيص المعين. يرجى تأكيد موعد الاستلام.');
    }

    protected function handleOtp(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        if (! $this->detectionService->verifyOtp($dynamicServiceOrder, $data['otp_code'] ?? '')) {
            throw ValidationException::withMessages(['otp_code' => 'رمز OTP غير صحيح']);
        }
        $this->detectionService->deductInventory($dynamicServiceOrder);
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);
        $this->detectionService->mergeWorkflowData($dynamicServiceOrder, ['delivered' => true]);
        $this->detectionService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'تم الاستلام. يرجى إكمال استبيان الرضا.');
        $this->appendHistory($dynamicServiceOrder, self::STEP_RECEIVE_ORDER, self::ACTION_VERIFY_OTP);
    }
}
