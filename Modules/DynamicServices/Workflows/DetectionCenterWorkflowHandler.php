<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;

class DetectionCenterWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_ATTENDANCE = 'attendance';
    public const STEP_SECOND_APPROVAL = 'second_approval';
    public const STEP_RECEIVE_ORDER = 'receive_order';

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
            self::STEP_INITIAL_APPROVAL => 'الاعتماد الأولي',
            self::STEP_ATTENDANCE => 'تسجيل الحضور',
            self::STEP_SECOND_APPROVAL => 'الاعتماد الثاني',
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

        return match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => $this->approvalActions(),
            self::STEP_ATTENDANCE => $this->attendanceActions(),
            self::STEP_SECOND_APPROVAL => $this->approvalActions('اعتماد', 'رفض'),
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
            $this->reject($beneficiaryOrder, $dynamicServiceOrder, $data['reason'] ?? null);

            return;
        }

        $step = $dynamicServiceOrder->workflow_step;

        if ($step === self::STEP_ATTENDANCE && $action === self::ACTION_MARK_NOT_ATTENDED) {
            $this->setWorkflowMeta($dynamicServiceOrder, 'attendance', 'not_attended');
            $this->appendHistory($dynamicServiceOrder, $step, $action);

            return;
        }

        match ($step) {
            self::STEP_INITIAL_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_ATTENDANCE, 1)
                : null,
            self::STEP_ATTENDANCE => $action === self::ACTION_MARK_ATTENDED
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_SECOND_APPROVAL, 2)
                : null,
            self::STEP_SECOND_APPROVAL => $action === self::ACTION_APPROVE
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_RECEIVE_ORDER, 3)
                : null,
            self::STEP_RECEIVE_ORDER => $action === self::ACTION_COMPLETE
                ? $this->complete($beneficiaryOrder, $dynamicServiceOrder)
                : null,
            default => null,
        };
    }
}
