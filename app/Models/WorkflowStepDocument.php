<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkflowStepDocument
 *
 * Optional, defines required or optional documents associated with a step.
 */
class WorkflowStepDocument extends Model
{
    use HasFactory;

    protected $table = 'workflow_step_documents';

    protected $fillable = [
        'workflow_step_id',
        'name',
        'code',
        'template_key',
        'is_required',
        'config',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'config' => 'array',
    ];

    public function step()
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }
}

