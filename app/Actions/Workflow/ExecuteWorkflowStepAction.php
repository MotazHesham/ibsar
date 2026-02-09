<?php

namespace App\Actions\Workflow;

use App\Models\User;
use App\Models\WorkflowInstance;
use App\Services\Workflow\WorkflowEngine;
use InvalidArgumentException;

/**
 * Example action: execute the current step of a workflow instance.
 *
 * Use from controllers when the user performs an action (approve, reject, submit, etc.).
 * Pass condition_key and any payload; the engine handles role checks and transitions.
 */
class ExecuteWorkflowStepAction
{
    public function __construct(
        protected WorkflowEngine $engine
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload  Must include 'condition_key' when the step has multiple outgoing transitions (e.g. approved, rejected).
     */
    public function run(WorkflowInstance $instance, ?User $user, array $payload = []): WorkflowInstance
    {
        return $this->engine->executeStep($instance, $user, $payload);
    }
}
