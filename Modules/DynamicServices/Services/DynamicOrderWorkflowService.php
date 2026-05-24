<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use Illuminate\Support\Facades\DB;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Services\TrainingWorkflowService;
use Modules\DynamicServices\Workflows\Contracts\WorkflowHandlerInterface;
use Modules\DynamicServices\Workflows\WorkflowResolver;

class DynamicOrderWorkflowService
{
    public function __construct(
        protected WorkflowResolver $workflowResolver
    ) {
    }

    public function initializeWorkflow(DynamicServiceOrder $dynamicServiceOrder, DynamicService $service): void
    {
        $handler = $this->workflowResolver->resolve($service);

        $dynamicServiceOrder->update([
            'workflow_step' => $handler->initialStep($service),
            'approval_stage' => 0,
            'workflow_data' => [
                'history' => [],
                'category' => $service->category,
                'service_type' => $service->service_type,
            ],
        ]);
    }

    public function getHandler(DynamicService $service): WorkflowHandlerInterface
    {
        return $this->workflowResolver->resolve($service);
    }

    public function getWorkflowContext(BeneficiaryOrder $beneficiaryOrder): ?array
    {
        $dynamicServiceOrder = $beneficiaryOrder->dynamicServiceOrder;

        if (! $dynamicServiceOrder?->dynamicService) {
            return null;
        }

        $service = $dynamicServiceOrder->dynamicService;
        $handler = $this->workflowResolver->resolve($service);
        $steps = $handler->steps($service);
        $currentStep = $dynamicServiceOrder->workflow_step ?? $handler->initialStep($service);

        $context = [
            'handler' => $handler,
            'service' => $service,
            'dynamicServiceOrder' => $dynamicServiceOrder,
            'steps' => $steps,
            'currentStep' => $currentStep,
            'currentStepLabel' => $handler->getStepLabel($service, $currentStep),
            'availableActions' => $handler->availableActions($beneficiaryOrder, $dynamicServiceOrder, $service),
            'isCompleted' => $handler->isCompleted($dynamicServiceOrder),
            'isRejected' => $handler->isRejected($dynamicServiceOrder),
            'stepKeys' => array_keys($steps),
        ];

        if ($service->category === DynamicService::CATEGORY_TRAINING) {
            $context = array_merge(
                $context,
                app(TrainingWorkflowService::class)->getViewData($beneficiaryOrder, $dynamicServiceOrder, $service)
            );
        }

        return $context;
    }

    public function processAction(BeneficiaryOrder $beneficiaryOrder, string $action, array $data = []): void
    {
        $dynamicServiceOrder = $beneficiaryOrder->dynamicServiceOrder()
            ->with('dynamicService')
            ->firstOrFail();

        $service = $dynamicServiceOrder->dynamicService;
        $handler = $this->workflowResolver->resolve($service);

        DB::transaction(function () use ($beneficiaryOrder, $dynamicServiceOrder, $service, $handler, $action, $data) {
            if (isset($data['note']) && $data['note']) {
                $beneficiaryOrder->update(['note' => $data['note']]);
            }

            if (isset($data['status_id']) && $data['status_id']) {
                $beneficiaryOrder->update(['status_id' => $data['status_id']]);
            }

            if (isset($data['specialist_id']) && $data['specialist_id']) {
                $beneficiaryOrder->update(['specialist_id' => $data['specialist_id']]);
            }

            $handler->processAction(
                $beneficiaryOrder->fresh(),
                $dynamicServiceOrder->fresh(),
                $service,
                $action,
                $data
            );
        });
    }

    public function backfillMissingWorkflow(DynamicServiceOrder $dynamicServiceOrder): void
    {
        if ($dynamicServiceOrder->workflow_step) {
            return;
        }

        $service = $dynamicServiceOrder->dynamicService;

        if ($service) {
            $this->initializeWorkflow($dynamicServiceOrder, $service);
        }
    }
}
