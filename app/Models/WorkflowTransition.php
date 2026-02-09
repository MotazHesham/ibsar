<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkflowTransition
 *
 * Defines an allowed transition between two steps in a workflow,
 * including optional conditions that must be satisfied.
 */
class WorkflowTransition extends Model
{
    use HasFactory;

    protected $table = 'workflow_transitions';

    protected $fillable = [
        'workflow_id',
        'from_step_id',
        'to_step_id',
        'name',
        'condition_key',
        'condition_expression',
        'is_default',
        'is_loopback',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_loopback' => 'boolean',
        'metadata' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function fromStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'from_step_id');
    }

    public function toStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'to_step_id');
    }
}

