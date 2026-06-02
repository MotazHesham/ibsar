<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use App\Services\WorkflowFinanceRequestService;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Services\SocialProgramsWorkflowService;

class SocialProgramsWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_SEND_PROGRAM_DETAILS = 'send_program_details';

    public const ACTION_APPROVE_PROJECTS = 'approve_projects';
    public const ACTION_SEND_DETAILS = 'send_program_details';

    public function __construct(
        protected SocialProgramsWorkflowService $programsService,
        protected WorkflowFinanceRequestService $financeRequestService,
    ) {
    }

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
            self::STEP_INITIAL_APPROVAL => 'اعتماد قسم المشاريع',
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
        return [];
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
            $this->programsService->notifyBeneficiaryUserAlert(
                $beneficiaryOrder,
                'نعتذر، لم يتم قبول طلبكم في البرنامج الاجتماعي. ' . ($data['reason'] ?? '')
            );

            return;
        }

        $validated = $this->programsService->validateAndExtract(
            $dynamicServiceOrder->workflow_step,
            $action,
            $data
        );

        match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => $action === self::ACTION_APPROVE_PROJECTS
                ? $this->handleProjectsApproval($beneficiaryOrder, $dynamicServiceOrder, $service)
                : null,
            self::STEP_SEND_PROGRAM_DETAILS => $action === self::ACTION_SEND_DETAILS
                ? $this->handleSendDetails($beneficiaryOrder, $dynamicServiceOrder, $service, $validated)
                : null,
            default => null,
        };
    }

    protected function handleProjectsApproval(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): void {
        $this->programsService->mergeWorkflowData($dynamicServiceOrder, [
            'projects' => ['approved' => true, 'approved_at' => now()->toDateTimeString()],
        ]);

        if ($beneficiaryOrder->accept_status !== 'yes') {
            $beneficiaryOrder->update(['accept_status' => 'yes']);
        }

        $this->moveToStep($dynamicServiceOrder, self::STEP_SEND_PROGRAM_DETAILS, 1);
        $this->appendHistory($dynamicServiceOrder, self::STEP_INITIAL_APPROVAL, self::ACTION_APPROVE_PROJECTS);

        $this->programsService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم اعتماد طلبكم في البرنامج الاجتماعي. سيتم إرسال تفاصيل البرنامج عند اكتمال العدد المستهدف.'
        );

        $this->financeRequestService->queue(
            beneficiaryOrder: $beneficiaryOrder,
            workflowCategory: DynamicService::CATEGORY_SOCIAL_PROGRAMS,
            workflowStep: self::STEP_INITIAL_APPROVAL,
            triggerAction: self::ACTION_APPROVE_PROJECTS,
            title: 'تسجيل مستفيد — برنامج: ' . $service->title,
            source: $dynamicServiceOrder,
            reference: $service,
        );
    }

    protected function handleSendDetails(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        array $validated
    ): void {
        $this->sendProgramDetails($service, $validated);
    }

    public function sendProgramDetails(DynamicService $service, array $validated): void
    {
        $message = $validated['program_details_message'] ?? '';
        $details = $validated['program_details'] ?? ['message' => $message, 'sent_at' => now()->toDateTimeString()];

        $orders = DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->where('workflow_step', self::STEP_SEND_PROGRAM_DETAILS)
            ->with('beneficiaryOrder')
            ->get();

        foreach ($orders as $dynamicServiceOrder) {
            $beneficiaryOrder = $dynamicServiceOrder->beneficiaryOrder;
            if (! $beneficiaryOrder) {
                continue;
            }

            $this->programsService->mergeWorkflowData($dynamicServiceOrder, ['program_details' => $details]);
            $this->complete($beneficiaryOrder, $dynamicServiceOrder);
            $this->appendHistory($dynamicServiceOrder, self::STEP_SEND_PROGRAM_DETAILS, self::ACTION_SEND_DETAILS, $details);
        }

        if ($orders->isNotEmpty()) {
            $this->programsService->notifyAllProgramBeneficiaries($service, $message);
        }
    }
}
