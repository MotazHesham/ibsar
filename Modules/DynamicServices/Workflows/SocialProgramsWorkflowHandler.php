<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;

class SocialProgramsWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_SEND_PROGRAM_DETAILS = 'send_program_details';

    public function category(): string
    {
        return DynamicService::CATEGORY_SOCIAL_PROGRAMS;
    }

    public function viewName(): string
    {
        return 'dynamicservices::workflows.social-programs.edit-status';
    }

    public function steps(DynamicService $service): array
    {
        return [
            self::STEP_INITIAL_APPROVAL => 'الاعتماد',
            self::STEP_SEND_PROGRAM_DETAILS => 'إرسال تفاصيل البرنامج',
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
            self::STEP_SEND_PROGRAM_DETAILS => $this->completeAction('تأكيد إرسال تفاصيل البرنامج'),
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
                ? $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_SEND_PROGRAM_DETAILS, 1)
                : null,
            self::STEP_SEND_PROGRAM_DETAILS => $action === self::ACTION_COMPLETE
                ? $this->complete($beneficiaryOrder, $dynamicServiceOrder)
                : null,
            default => null,
        };
    }
}
