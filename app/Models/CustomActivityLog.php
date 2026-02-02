<?php 

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class CustomActivityLog extends SpatieActivity
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'ip_address',
        'user_agent', 
    ];  
}