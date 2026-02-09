<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class WorkflowInstance
 *
 * Represents a running workflow for a specific business entity
 * (e.g. a training program application, social aid request, etc.).
 */
class WorkflowInstance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'workflow_instances';

    protected $fillable = [
        'workflow_id',
        'entity_type',
        'entity_id',
        'current_step_id',
        'status',
        'started_by_id',
        'finished_by_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    public function entity()
    {
        return $this->morphTo(null, 'entity_type', 'entity_id');
    }

    public function logs()
    {
        return $this->hasMany(WorkflowLog::class, 'workflow_instance_id');
    }
}

