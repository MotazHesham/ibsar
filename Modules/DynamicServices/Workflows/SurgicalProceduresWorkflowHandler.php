<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Services\SurgicalProceduresWorkflowService;

class SurgicalProceduresWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_WAITING_LIST = 'waiting_list';
    public const STEP_CLINIC_ACCOUNT = 'clinic_account';
    public const STEP_FINANCIAL_APPROVAL = 'second_approval';
    public const STEP_PERFORM_OPERATION = 'perform_operation';

    public const ACTION_APPROVE_RECEPTION = 'approve_reception';
    public const ACTION_TRANSFER_CLINIC = 'transfer_clinic';
    public const ACTION_CLOSE_CASE = 'close_case';
    public const ACTION_SUBMIT_CLINIC_REPORT = 'submit_clinic_report';
    public const ACTION_REJECT_CLINIC = 'reject_clinic';
    public const ACTION_SUBMIT_CONTRIBUTION = 'submit_contribution';
    public const ACTION_APPROVE_FINANCIAL = 'approve_financial';
    public const ACTION_REJECT_FINANCIAL = 'reject_financial';
    public const ACTION_SUBMIT_OPERATION = 'submit_operation';

    public function __construct(
        protected SurgicalProceduresWorkflowService $surgicalService
    ) {
    }

    public function category(): string
    {
        return DynamicService::CATEGORY_SURGICAL_PROCEDURES;
    }

    public function viewName(): string
    {
        return 'dynamicservices::workflows.surgical-procedures.edit-status';
    }

    public function steps(DynamicService $service): array
    {
        return [
            self::STEP_INITIAL_APPROVAL => 'اعتماد الاستقبال',
            self::STEP_WAITING_LIST => 'قائمة الانتظار',
            self::STEP_CLINIC_ACCOUNT => 'حساب العيادة',
            self::STEP_FINANCIAL_APPROVAL => 'اعتماد المالية',
            self::STEP_PERFORM_OPERATION => 'إجراء العملية',
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

        return match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => [
                ['key' => self::ACTION_APPROVE_RECEPTION, 'label' => 'يعتمد — إضافة لقائمة الانتظار', 'type' => 'success'],
                ['key' => self::ACTION_REJECT, 'label' => 'لا يعتمد', 'type' => 'danger', 'requires_reason' => true],
            ],
            self::STEP_WAITING_LIST => [
                ['key' => self::ACTION_TRANSFER_CLINIC, 'label' => 'تحويل للعيادة', 'type' => 'primary', 'form_submit' => true],
                ['key' => self::ACTION_CLOSE_CASE, 'label' => 'إغلاق الحالة', 'type' => 'warning', 'form_submit' => true],
            ],
            self::STEP_CLINIC_ACCOUNT => $this->clinicActions($dynamicServiceOrder),
            self::STEP_FINANCIAL_APPROVAL => [
                ['key' => self::ACTION_APPROVE_FINANCIAL, 'label' => 'يعتمد (المالية)', 'type' => 'success', 'form_submit' => true],
                ['key' => self::ACTION_REJECT_FINANCIAL, 'label' => 'لا يعتمد (لم يسدد)', 'type' => 'danger', 'requires_reason' => true],
            ],
            self::STEP_PERFORM_OPERATION => [[
                'key' => self::ACTION_SUBMIT_OPERATION,
                'label' => 'تأكيد إجراء العملية',
                'type' => 'success',
                'form_submit' => true,
            ]],
            default => [],
        };
    }

    protected function clinicActions(DynamicServiceOrder $dynamicServiceOrder): array
    {
        $clinic = $dynamicServiceOrder->workflow_data['clinic'] ?? [];
        if (empty($clinic['submitted_at'])) {
            return [[
                'key' => self::ACTION_SUBMIT_CLINIC_REPORT,
                'label' => 'حفظ بيانات العيادة',
                'type' => 'primary',
                'form_submit' => true,
            ], [
                'key' => self::ACTION_REJECT_CLINIC,
                'label' => 'رفض (العيادة)',
                'type' => 'danger',
                'requires_reason' => true,
            ]];
        }
        if (empty($dynamicServiceOrder->workflow_data['contribution']['submitted_at'] ?? null)) {
            return [[
                'key' => self::ACTION_SUBMIT_CONTRIBUTION,
                'label' => 'تحديد المساهمة (الباحث)',
                'type' => 'success',
                'form_submit' => true,
            ]];
        }

        return [];
    }

    public function processAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        array $data = []
    ): void {
        $step = $dynamicServiceOrder->workflow_step;
        $validated = $this->surgicalService->validateAndExtract($step, $action, $data);

        if ($action === self::ACTION_REJECT || $action === self::ACTION_REJECT_CLINIC || $action === self::ACTION_REJECT_FINANCIAL) {
            $this->handleReject($beneficiaryOrder, $dynamicServiceOrder, $action, $data);

            return;
        }

        match (true) {
            $step === self::STEP_INITIAL_APPROVAL && $action === self::ACTION_APPROVE_RECEPTION
                => $this->handleReceptionApproval($beneficiaryOrder, $dynamicServiceOrder),
            $step === self::STEP_WAITING_LIST && $action === self::ACTION_TRANSFER_CLINIC
                => $this->handleTransfer($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_WAITING_LIST && $action === self::ACTION_CLOSE_CASE
                => $this->handleCloseCase($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_CLINIC_ACCOUNT && $action === self::ACTION_SUBMIT_CLINIC_REPORT
                => $this->handleClinicReport($dynamicServiceOrder, $validated),
            $step === self::STEP_CLINIC_ACCOUNT && $action === self::ACTION_SUBMIT_CONTRIBUTION
                => $this->handleContribution($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_FINANCIAL_APPROVAL && $action === self::ACTION_APPROVE_FINANCIAL
                => $this->handleFinancialApproval($beneficiaryOrder, $dynamicServiceOrder, $validated),
            $step === self::STEP_PERFORM_OPERATION && $action === self::ACTION_SUBMIT_OPERATION
                => $this->handleOperation($beneficiaryOrder, $dynamicServiceOrder, $validated),
            default => null,
        };
    }

    protected function handleReject(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, string $action, array $data): void
    {
        $reason = $data['reason'] ?? null;
        $this->reject($beneficiaryOrder, $dynamicServiceOrder, $reason);
        $this->surgicalService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'نعتذر، لم يتم قبول طلب العملية الجراحية. ' . ($reason ? 'السبب: ' . $reason : '')
        );
    }

    protected function handleReceptionApproval(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, [
            'waiting_list' => ['added_at' => now()->toDateTimeString()],
        ]);
        if ($beneficiaryOrder->accept_status !== 'yes') {
            $beneficiaryOrder->update(['accept_status' => 'yes']);
        }
        $this->moveToStep($dynamicServiceOrder, self::STEP_WAITING_LIST, 1);
        $this->appendHistory($dynamicServiceOrder, self::STEP_INITIAL_APPROVAL, self::ACTION_APPROVE_RECEPTION);
        $this->surgicalService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم قبول طلبكم وإضافتكم لقائمة انتظار العمليات الجراحية.'
        );
    }

    protected function handleTransfer(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_CLINIC_ACCOUNT, 2);
        $this->appendHistory($dynamicServiceOrder, self::STEP_WAITING_LIST, self::ACTION_TRANSFER_CLINIC, $validated['transfer'] ?? []);
        $this->surgicalService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تحويل طلبكم إلى العيادة: ' . ($validated['transfer']['clinic_name'] ?? '')
        );
    }

    protected function handleCloseCase(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);
        $beneficiaryOrder->update(['done' => 1]);
        $this->appendHistory($dynamicServiceOrder, self::STEP_WAITING_LIST, self::ACTION_CLOSE_CASE, $validated['close_case'] ?? []);
    }

    protected function handleClinicReport(DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->appendHistory($dynamicServiceOrder, self::STEP_CLINIC_ACCOUNT, self::ACTION_SUBMIT_CLINIC_REPORT, $validated['clinic'] ?? []);
    }

    protected function handleContribution(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, 3);
        $this->appendHistory($dynamicServiceOrder, self::STEP_CLINIC_ACCOUNT, self::ACTION_SUBMIT_CONTRIBUTION, $validated['contribution'] ?? []);
        $this->surgicalService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تحديد قيمة المساهمة. يرجى السداد ورفع الإيصال من صفحة الطلب.'
        );
    }

    protected function handleFinancialApproval(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_PERFORM_OPERATION, 4);
        $this->appendHistory($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, self::ACTION_APPROVE_FINANCIAL);
        $this->surgicalService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'تم اعتماد المساهمة المالية. سيتم إشعار العيادة باعتماد العملية.');
    }

    protected function handleOperation(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $validated): void
    {
        $this->surgicalService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->complete($beneficiaryOrder, $dynamicServiceOrder);
        $this->appendHistory($dynamicServiceOrder, self::STEP_PERFORM_OPERATION, self::ACTION_SUBMIT_OPERATION, $validated['operation'] ?? []);
        $this->surgicalService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'تم إجراء العملية وتسجيلها في ملفكم.');
    }
}
