<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;

class SurgicalProceduresWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_CLINIC_ACCOUNT = 'clinic_account';
    public const STEP_SECOND_APPROVAL = 'second_approval';
    public const STEP_PERFORM_OPERATION = 'perform_operation';

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
            self::STEP_INITIAL_APPROVAL => 'الاعتماد الأولي',
            self::STEP_CLINIC_ACCOUNT => 'حساب العيادة',
            self::STEP_SECOND_APPROVAL => 'الاعتماد الثاني',
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
            self::STEP_INITIAL_APPROVAL,
            self::STEP_SECOND_APPROVAL => $this->approvalActions(),
            self::STEP_CLINIC_ACCOUNT => $this->advanceAction('تم حساب العيادة'),
            self::STEP_PERFORM_OPERATION => $this->completeAction('تأكيد إجراء العملية'),
            default => [],
        };
    }

    public function processAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        array $data = []
    ): void {
        if ($action === self::ACTION_REJECT) {
            $this->reject($beneficiaryOrder, $dynamicServiceOrder, $data['reason'] ?? null);

            return;
        }

        match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_CLINIC_ACCOUNT, 1)
                : null,
            self::STEP_CLINIC_ACCOUNT => $action === self::ACTION_ADVANCE
                ? $this->moveToStep($dynamicServiceOrder, self::STEP_SECOND_APPROVAL)
                : null,
            self::STEP_SECOND_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_PERFORM_OPERATION, 2)
                : null,
            self::STEP_PERFORM_OPERATION => $action === self::ACTION_COMPLETE
                ? $this->complete($beneficiaryOrder, $dynamicServiceOrder)
                : null,
            default => null,
        };
    }
}
