<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicServiceWorkflowTransition extends Model
{
    use HasFactory;

    public $table = 'dynamic_service_workflow_transitions';

    protected $fillable = [
        'workflow_id',
        'from_status',
        'to_status',
        'notes',
        'user_id',
        'data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(DynamicServiceWorkflow::class, 'workflow_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

