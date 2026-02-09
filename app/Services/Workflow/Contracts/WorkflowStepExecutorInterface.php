<?php

namespace App\Services\Workflow\Contracts;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Services\Workflow\WorkflowStepExecutionResult;

/**
 * Strategy interface for executing workflow steps of different types.
 *
 * Implementations MUST remain generic and must not contain module-specific
 * business rules. They can, however, dispatch jobs, events, or hooks that
 * application modules can listen to.
 */
interface WorkflowStepExecutorInterface
{
    /**
     * Whether this executor can handle the given step type.
     */
    public function supports(string $stepType): bool;

    /**
     * Execute the given step and return an execution result.
     *
     * @param WorkflowInstance $instance Running workflow instance.
     * @param WorkflowStep     $step     Step to execute.
     * @param User|null        $user     Currently authenticated user (if any).
     * @param array            $payload  Arbitrary payload from UI/consumers.
     */
    public function execute(
        WorkflowInstance $instance,
        WorkflowStep $step,
        ?User $user = null,
        array $payload = []
    ): WorkflowStepExecutionResult;
}

