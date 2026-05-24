<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;

class AssistanceWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_FIRST_APPROVAL = 'first_approval';
    public const STEP_SECOND_APPROVAL = 'second_approval';
    public const STEP_THIRD_APPROVAL = 'third_approval';
    public const STEP_RECEIVE_ORDER = 'receive_order';

    public const SUBTYPE_IN_KIND = 'in_kind';
    public const SUBTYPE_FINANCIAL = 'financial';

    public function category(): string
    {
        return DynamicService::CATEGORY_ASSISTANCE;
    }

    public function viewName(): string
    {
        return 'dynamicservices::workflows.assistance.edit-status';
    }

    public function steps(DynamicService $service): array
    {
        if ($this->isFinancial($service)) {
            return [
                self::STEP_FIRST_APPROVAL => 'الاعتماد الأول',
                self::STEP_SECOND_APPROVAL => 'الاعتماد الثاني',
                self::STEP_THIRD_APPROVAL => 'الاعتماد الثالث',
                self::STEP_COMPLETED => 'مكتمل',
            ];
        }

        return [
            self::STEP_FIRST_APPROVAL => 'الاعتماد الأول',
            self::STEP_SECOND_APPROVAL => 'الاعتماد الثاني',
            self::STEP_RECEIVE_ORDER => 'استلام الطلب',
            self::STEP_COMPLETED => 'مكتمل',
        ];
    }

    public function initialStep(DynamicService $service): string
    {
        return self::STEP_FIRST_APPROVAL;
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
            self::STEP_FIRST_APPROVAL,
            self::STEP_SECOND_APPROVAL,
            self::STEP_THIRD_APPROVAL => $this->approvalActions(),
            self::STEP_RECEIVE_ORDER => $this->completeAction('تأكيد استلام الطلب'),
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
            if ($dynamicServiceOrder->workflow_step === self::STEP_FIRST_APPROVAL) {
                $this->reject($beneficiaryOrder, $dynamicServiceOrder, $data['reason'] ?? null);

                return;
            }

            $this->moveToStep($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, 0);
            $this->appendHistory($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, self::ACTION_REJECT, [
                'reason' => $data['reason'] ?? null,
            ]);

            return;
        }

        if ($this->isFinancial($service)) {
            $this->processFinancialAction($beneficiaryOrder, $dynamicServiceOrder, $action);

            return;
        }

        $this->processInKindAction($beneficiaryOrder, $dynamicServiceOrder, $action);
    }

    protected function processInKindAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        string $action
    ): void {
        match ($dynamicServiceOrder->workflow_step) {
            self::STEP_FIRST_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_SECOND_APPROVAL, 1)
                : null,
            self::STEP_SECOND_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_RECEIVE_ORDER, 2)
                : null,
            self::STEP_RECEIVE_ORDER => $action === self::ACTION_COMPLETE
                ? $this->complete($beneficiaryOrder, $dynamicServiceOrder)
                : null,
            default => null,
        };
    }

    protected function processFinancialAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        string $action
    ): void {
        match ($dynamicServiceOrder->workflow_step) {
            self::STEP_FIRST_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_SECOND_APPROVAL, 1)
                : null,
            self::STEP_SECOND_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_THIRD_APPROVAL, 2)
                : null,
            self::STEP_THIRD_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->complete($beneficiaryOrder, $dynamicServiceOrder)
                : null,
            default => null,
        };
    }

    protected function isFinancial(DynamicService $service): bool
    {
        return $service->service_type === self::SUBTYPE_FINANCIAL;
    }
}
