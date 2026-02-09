<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkflowLog
 *
 * Provides a full audit trail of all workflow-related actions.
 */
class WorkflowLog extends Model
{
    use HasFactory;

    protected $table = 'workflow_logs';

    protected $fillable = [
        'workflow_instance_id',
        'workflow_id',
        'step_id',
        'from_step_id',
        'to_step_id',
        'action',
        'performer_type',
        'performer_id',
        'performer_role',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function instance()
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function step()
    {
        return $this->belongsTo(WorkflowStep::class, 'step_id');
    }

    public function performer()
    {
        return $this->morphTo();
    }
}

