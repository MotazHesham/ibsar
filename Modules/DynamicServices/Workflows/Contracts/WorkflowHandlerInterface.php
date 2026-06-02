<?php

namespace Modules\DynamicServices\Workflows\Contracts;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;

interface WorkflowHandlerInterface
{
    public function category(): string;

    /**
     * @return array<string, string> step_key => Arabic label
     */
    public function steps(DynamicService $service): array;

    public function initialStep(DynamicService $service): string;

    public function getStepLabel(DynamicService $service, string $step): string;

    /**
     * @return array<int, array{key: string, label: string, type: string, options?: array<string, string>}>
     */
    public function availableActions(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array;

    public function processAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        array $data = []
    ): void;

    public function isCompleted(DynamicServiceOrder $dynamicServiceOrder): bool;

    public function isRejected(DynamicServiceOrder $dynamicServiceOrder): bool;

    public function viewName(): string;
}
