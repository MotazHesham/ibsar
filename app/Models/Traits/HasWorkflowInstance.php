<?php

namespace App\Models\Traits;

use App\Models\WorkflowInstance;

/**
 * Add to any model that can have a running workflow instance (new engine).
 *
 * Provides workflowInstance() and latestWorkflowInstance() relations.
 */
trait HasWorkflowInstance
{
    /**
     * The single active workflow instance for this entity (new engine).
     * Use when at most one running instance per entity is expected.
     */
    public function workflowInstance()
    {
        return $this->morphOne(WorkflowInstance::class, 'entity')
            ->whereIn('status', ['running', 'on_hold'])
            ->latest('id');
    }

    /**
     * All workflow instances for this entity (including completed/cancelled).
     */
    public function workflowInstances()
    {
        return $this->morphMany(WorkflowInstance::class, 'entity');
    }
}
