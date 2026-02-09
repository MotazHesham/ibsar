<?php

namespace App\Listeners;

use App\Events\WorkflowStepAutoExecuted;
use Illuminate\Support\Facades\Log;

/**
 * Default listener for auto-step execution.
 *
 * Logs the event; add your own logic (notifications, document generation)
 * here or register additional listeners in EventServiceProvider.
 */
class HandleWorkflowStepAutoExecuted
{
    public function handle(WorkflowStepAutoExecuted $event): void
    {
        Log::channel('single')->info('Workflow auto step executed', [
            'workflow_instance_id' => $event->instance->id,
            'step_key' => $event->step->key,
            'step_name' => $event->step->name,
        ]);

        // Example: dispatch notifications or document generation by step key
        // if ($event->step->key === 'send_acceptance_notification') { ... }
    }
}
