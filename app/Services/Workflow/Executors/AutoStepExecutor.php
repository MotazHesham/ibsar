<?php

namespace App\Services\Workflow\Executors;

use App\Events\WorkflowStepAutoExecuted;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Services\Workflow\Contracts\WorkflowStepExecutorInterface;
use App\Services\Workflow\WorkflowStepExecutionResult;
use Illuminate\Support\Facades\Event;

/**
 * Executor for "auto" steps.
 *
 * Auto steps are executed by the system without human interaction.
 * The concrete business actions are delegated to events/listeners
 * or queued jobs registered elsewhere in the application.
 */
class AutoStepExecutor implements WorkflowStepExecutorInterface
{
    public function supports(string $stepType): bool
    {
        return $stepType === 'auto';
    }

    public function execute(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?User $user = null,
        array $payload = []
    ): WorkflowStepExecutionResult {
        // Dispatch event so listeners can perform concrete actions
        // (notifications, document generation, etc.).
        Event::dispatch(new WorkflowStepAutoExecuted($instance, $step, $payload));

        // Auto steps usually complete successfully and move forward
        // with a standard "completed" condition key.
        $conditionKey = $payload['condition_key'] ?? 'completed';

        return new WorkflowStepExecutionResult(
            step: $step,
            conditionKey: $conditionKey,
            dataUpdates: [],
            notes: $payload['notes'] ?? null,
            metadata: [
                'auto' => true,
            ]
        );
    }
}

