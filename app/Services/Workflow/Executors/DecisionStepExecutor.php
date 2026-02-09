<?php

namespace App\Services\Workflow\Executors;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Services\Workflow\Contracts\WorkflowStepExecutorInterface;
use App\Services\Workflow\WorkflowStepExecutionResult;

/**
 * Executor for "decision" steps.
 *
 * A decision step typically does not involve data entry but may
 * still be triggered by a human (e.g. choosing a branch). The
 * actual routing is controlled by transition conditions rather
 * than heavy logic here.
 */
class DecisionStepExecutor implements WorkflowStepExecutorInterface
{
    public function supports(string $stepType): bool
    {
        return $stepType === 'decision';
    }

    public function execute(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?User $user = null,
        array $payload = []
    ): WorkflowStepExecutionResult {
        // Map simple user action to condition key if provided.
        $conditionKey = $payload['condition_key'] ?? null;

        return new WorkflowStepExecutionResult(
            step: $step,
            conditionKey: $conditionKey,
            dataUpdates: [],
            notes: $payload['notes'] ?? null,
            metadata: []
        );
    }
}

