<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkflowStep
 *
 * Defines a single step in a workflow definition.
 * Supports four generic types:
 *  - human   : requires human interaction (review, approval, data entry)
 *  - decision: evaluates conditions and routes to the next step
 *  - auto    : automatic system step (notifications, document generation, etc.)
 *  - waiting : waits for time/capacity/external condition before moving on
 */
class WorkflowStep extends Model
{
    use HasFactory;

    protected $table = 'workflow_steps';

    protected $fillable = [
        'workflow_id',
        'key',
        'name',
        'type',
        'position',
        'is_start',
        'is_end',
        'allowed_roles',
        'policy_ability',
        'config',
    ];

    protected $casts = [
        'is_start' => 'boolean',
        'is_end' => 'boolean',
        'allowed_roles' => 'array',
        'config' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function outgoingTransitions()
    {
        return $this->hasMany(WorkflowTransition::class, 'from_step_id');
    }

    public function incomingTransitions()
    {
        return $this->hasMany(WorkflowTransition::class, 'to_step_id');
    }

    public function documents()
    {
        return $this->hasMany(WorkflowStepDocument::class, 'workflow_step_id');
    }
}

