<?php

namespace App\Services\Workflow\Executors;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Services\Workflow\Contracts\WorkflowStepExecutorInterface;
use App\Services\Workflow\WorkflowStepExecutionResult;

/**
 * Executor for "waiting" steps.
 *
 * Waiting steps typically put the workflow instance on hold until
 * some external condition is met (time window, capacity, external system).
 * The workflow engine will not attempt to auto-transition further after
 * this executor runs; a later trigger must re-execute the step.
 */
class WaitingStepExecutor implements WorkflowStepExecutorInterface
{
    public function supports(string $stepType): bool
    {
        return $stepType === 'waiting';
    }

    public function execute(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?User $user = null,
        array $payload = []
    ): WorkflowStepExecutionResult {
        // The engine will typically set the instance status to "on_hold"
        // when this executor is used; here we just indicate that further
        // automatic processing should stop.

        return new WorkflowStepExecutionResult(
            step: $step,
            conditionKey: null,
            dataUpdates: [],
            notes: $payload['notes'] ?? null,
            metadata: [],
            stopProcessing: true
        );
    }
}

