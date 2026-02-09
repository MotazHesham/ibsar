<?php

namespace App\Services\Workflow;

use App\Models\WorkflowStep;

/**
 * Value object representing the result of executing a workflow step.
 */
class WorkflowStepExecutionResult
{
    /**
     * @param WorkflowStep $step           The step that was executed.
     * @param string|null  $conditionKey   Simple condition key for transition selection (approved, rejected, etc.).
     * @param array        $dataUpdates    Key/value pairs to be merged into the instance data.
     * @param string|null  $notes          Optional human-readable notes to be logged.
     * @param array        $metadata       Additional metadata to be stored in the log entry.
     * @param bool         $stopProcessing If true, the engine should not attempt to auto-transition further (e.g. waiting steps).
     */
    public function __construct(
        public WorkflowStep $step,
        public ?string $conditionKey = null,
        public array $dataUpdates = [],
        public ?string $notes = null,
        public array $metadata = [],
        public bool $stopProcessing = false,
    ) {
    }
}

