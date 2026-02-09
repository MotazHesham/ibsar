<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Workflow
 *
 * Represents a reusable workflow definition (type-level).
 * Examples: training_program, social_aid, community_program.
 *
 * NOTE: This model is generic and MUST NOT contain any
 * module-specific or legacy dynamic service workflow logic.
 */
class Workflow extends Model
{
    use HasFactory;

    protected $table = 'workflows';

    protected $fillable = [
        'key',
        'name',
        'description',
        'entity_type',
        'version',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function steps()
    {
        return $this->hasMany(WorkflowStep::class)
            ->orderBy('position');
    }

    public function transitions()
    {
        return $this->hasMany(WorkflowTransition::class);
    }

    public function instances()
    {
        return $this->hasMany(WorkflowInstance::class);
    }

    public function startStep()
    {
        return $this->hasOne(WorkflowStep::class)->where('is_start', true);
    }
}

