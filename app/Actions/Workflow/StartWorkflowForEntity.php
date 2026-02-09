<?php

namespace App\Actions\Workflow;

use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\User;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Example action: start a workflow for a given entity.
 *
 * Use this from controllers or jobs. The engine is injected;
 * no module-specific logic lives here.
 */
class StartWorkflowForEntity
{
    public function __construct(
        protected WorkflowEngine $engine
    ) {
    }

    /**
     * @param  array<string, mixed>  $initialData
     */
    public function run(Workflow $workflow, Model $entity, ?User $user = null, array $initialData = []): WorkflowInstance
    {
        if (! $workflow->is_active) {
            throw new InvalidArgumentException("Workflow [{$workflow->key}] is not active.");
        }

        return $this->engine->start($workflow, $entity, $user, $initialData);
    }
}
