<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Workflows\Contracts\WorkflowHandlerInterface;

abstract class AbstractWorkflowHandler implements WorkflowHandlerInterface
{
    public const STEP_REJECTED = 'rejected';
    public const STEP_COMPLETED = 'completed';

    public const ACTION_APPROVE = 'approve';
    public const ACTION_REJECT = 'reject';
    public const ACTION_MARK_ATTENDED = 'mark_attended';
    public const ACTION_MARK_NOT_ATTENDED = 'mark_not_attended';
    public const ACTION_ADVANCE = 'advance';
    public const ACTION_COMPLETE = 'complete';

    public function getStepLabel(DynamicService $service, string $step): string
    {
        return $this->steps($service)[$step] ?? $step;
    }

    public function isCompleted(DynamicServiceOrder $dynamicServiceOrder): bool
    {
        return $dynamicServiceOrder->workflow_step === self::STEP_COMPLETED;
    }

    public function isRejected(DynamicServiceOrder $dynamicServiceOrder): bool
    {
        return $dynamicServiceOrder->workflow_step === self::STEP_REJECTED;
    }

  protected function approvalActions(string $approveLabel = 'اعتماد', string $rejectLabel = 'رفض'): array
    {
        return [
            [
                'key' => self::ACTION_APPROVE,
                'label' => $approveLabel,
                'type' => 'primary',
            ],
            [
                'key' => self::ACTION_REJECT,
                'label' => $rejectLabel,
                'type' => 'danger',
                'requires_reason' => true,
            ],
        ];
    }

    protected function attendanceActions(): array
    {
        return [
            [
                'key' => self::ACTION_MARK_ATTENDED,
                'label' => 'حضر',
                'type' => 'success',
            ],
            [
                'key' => self::ACTION_MARK_NOT_ATTENDED,
                'label' => 'لم يحضر',
                'type' => 'warning',
            ],
        ];
    }

    protected function advanceAction(string $label = 'متابعة'): array
    {
        return [
            [
                'key' => self::ACTION_ADVANCE,
                'label' => $label,
                'type' => 'primary',
            ],
        ];
    }

    protected function completeAction(string $label = 'إنهاء الطلب'): array
    {
        return [
            [
                'key' => self::ACTION_COMPLETE,
                'label' => $label,
                'type' => 'success',
            ],
        ];
    }

    protected function moveToStep(
        DynamicServiceOrder $dynamicServiceOrder,
        string $step,
        ?int $approvalStage = null
    ): void {
        $updates = ['workflow_step' => $step];

        if ($approvalStage !== null) {
            $updates['approval_stage'] = $approvalStage;
        }

        $dynamicServiceOrder->update($updates);
    }

    protected function reject(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        ?string $reason = null
    ): void {
        $this->moveToStep($dynamicServiceOrder, self::STEP_REJECTED);

        $beneficiaryOrder->update([
            'accept_status' => 'no',
            'refused_reason' => $reason,
            'done' => 0,
        ]);

        $this->appendHistory($dynamicServiceOrder, self::STEP_REJECTED, self::ACTION_REJECT, [
            'reason' => $reason,
        ]);
    }

    protected function complete(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder
    ): void {
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);

        $beneficiaryOrder->update([
            'accept_status' => 'yes',
            'done' => 1,
        ]);

        $this->appendHistory($dynamicServiceOrder, self::STEP_COMPLETED, self::ACTION_COMPLETE);
    }

    protected function approveAndMove(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        string $nextStep,
        ?int $approvalStage = null
    ): void {
        if ($dynamicServiceOrder->approval_stage === 0) {
            $beneficiaryOrder->update(['accept_status' => 'yes']);
        }

        $this->moveToStep($dynamicServiceOrder, $nextStep, $approvalStage);

        $this->appendHistory($dynamicServiceOrder, $nextStep, self::ACTION_APPROVE);
    }

    protected function appendHistory(
        DynamicServiceOrder $dynamicServiceOrder,
        string $step,
        string $action,
        array $meta = []
    ): void {
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

    protected function setWorkflowMeta(DynamicServiceOrder $dynamicServiceOrder, string $key, mixed $value): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $workflowData[$key] = $value;
        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
    }

    protected function stepKeys(DynamicService $service): array
    {
        return array_keys($this->steps($service));
    }

    protected function nextStepAfter(DynamicService $service, string $currentStep): ?string
    {
        $keys = $this->stepKeys($service);
        $index = array_search($currentStep, $keys, true);

        if ($index === false) {
            return null;
        }

        return $keys[$index + 1] ?? null;
    }
}
