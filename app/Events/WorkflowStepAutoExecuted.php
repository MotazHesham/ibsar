<?php

namespace App\Events;

use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when the workflow engine executes an "auto" step.
 *
 * Listen to this event to perform module-specific actions:
 * e.g. send notifications, generate documents, call external APIs.
 */
class WorkflowStepAutoExecuted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorkflowInstance $instance,
        public WorkflowStep $step,
        public array $payload = []
    ) {
    }
}
