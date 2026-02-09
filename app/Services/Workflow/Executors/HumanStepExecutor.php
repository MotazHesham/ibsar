<?php

namespace App\Services\Workflow\Executors;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Services\Workflow\Contracts\WorkflowStepExecutorInterface;
use App\Services\Workflow\WorkflowStepExecutionResult;

/**
 * Generic executor for "human" steps.
 *
 * Responsibilities:
 *  - Accept human input (payload) and attach it to the instance data.
 *  - Return a simple condition key (e.g. approved, rejected, completed)
 *    which is typically mapped from the UI action (button) that was used.
 *
 * All business-specific handling (e.g. what "approved" means for a
 * training program) is outside this executor and should live in
 * module-specific listeners, jobs, or domain services.
 */
class HumanStepExecutor implements WorkflowStepExecutorInterface
{
    public function supports(string $stepType): bool
    {
        return $stepType === 'human';
    }

    public function execute(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?User $user = null,
        array $payload = []
    ): WorkflowStepExecutionResult {
        // Assume the caller has already validated/filtered the payload.
        $dataUpdates = [
            'steps' => [
                $step->key => [
                    'payload' => $payload,
                    'performed_by' => $user?->id,
                    'performed_at' => now()->toDateTimeString(),
                ],
            ],
        ];

        // Condition key is expected from payload['condition_key'] if present.
        $conditionKey = $payload['condition_key'] ?? null;

        return new WorkflowStepExecutionResult(
            step: $step,
            conditionKey: $conditionKey,
            dataUpdates: $dataUpdates,
            notes: $payload['notes'] ?? null,
            metadata: []
        );
    }
}

