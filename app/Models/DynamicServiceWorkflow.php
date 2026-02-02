<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicServiceWorkflow extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'dynamic_service_workflows_base';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'dynamic_service_order_id',
        'category',
        'current_status',
        'notes',
        'refused_reason',
        'specialist_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Common workflow statuses
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_REFUSED = 'refused';
    public const STATUS_COMPLETED = 'completed';

    public const STATUS_LABELS = [
        self::STATUS_PENDING_REVIEW => 'قيد المراجعة',
        self::STATUS_REFUSED => 'مرفوض',
        self::STATUS_COMPLETED => 'مكتمل',
    ];

    public function dynamicServiceOrder()
    {
        return $this->belongsTo(DynamicServiceOrder::class, 'dynamic_service_order_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function transitions()
    {
        return $this->hasMany(DynamicServiceWorkflowTransition::class, 'workflow_id');
    }

    public function getStatusLabelAttribute()
    {
        // Get category-specific status labels
        $categoryLabels = $this->getCategoryStatusLabels();
        return $categoryLabels[$this->current_status] ?? $this->current_status;
    }

    protected function getCategoryStatusLabels()
    {
        switch ($this->category) {
            case 'training':
                return DynamicServiceWorkflowTraining::STATUS_LABELS;
            case 'assistance':
                return DynamicServiceWorkflowAssistance::STATUS_LABELS;
            case 'social_programs':
                return DynamicServiceWorkflowSocialPrograms::STATUS_LABELS;
            default:
                return self::STATUS_LABELS;
        }
    }

    /**
     * Get the category-specific workflow data
     */
    public function categoryData()
    {
        switch ($this->category) {
            case 'training':
                return $this->training;
            case 'assistance':
                return $this->assistance;
            case 'social_programs':
                return $this->socialPrograms;
            default:
                return null;
        }
    }

    /**
     * Get category-specific workflow (polymorphic relationship)
     */
    public function training()
    {
        return $this->hasOne(DynamicServiceWorkflowTraining::class, 'workflow_id');
    }

    public function assistance()
    {
        return $this->hasOne(DynamicServiceWorkflowAssistance::class, 'workflow_id');
    }

    public function socialPrograms()
    {
        return $this->hasOne(DynamicServiceWorkflowSocialPrograms::class, 'workflow_id');
    }

    /**
     * Check if workflow can transition to a status
     */
    public function canTransitionTo($status)
    {
        switch ($this->category) {
            case 'training':
                return $this->training?->canTransitionTo($status) ?? false;
            case 'assistance':
                return $this->assistance?->canTransitionTo($status) ?? false;
            case 'social_programs':
                return $this->socialPrograms?->canTransitionTo($status) ?? false;
            default:
                return false;
        }
    }

    /**
     * Get all workflow data (base + category-specific)
     */
    public function getAllAttributes()
    {
        $attributes = $this->getAttributes();
        
        if ($this->category === 'training' && $this->training) {
            $attributes = array_merge($attributes, $this->training->getAttributes());
        } elseif ($this->category === 'assistance' && $this->assistance) {
            $attributes = array_merge($attributes, $this->assistance->getAttributes());
        } elseif ($this->category === 'social_programs' && $this->socialPrograms) {
            $attributes = array_merge($attributes, $this->socialPrograms->getAttributes());
        }
        
        return $attributes;
    }
}
