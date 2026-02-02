<?php

namespace App\Utils; 

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = LogOptions::defaults()
            ->logOnly(method_exists($this, 'getLogAttributes') ? $this->getLogAttributes() : ["*"])
            ->logExcept(['updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
        if (method_exists($this, 'getActivityDescriptionForEvent')) {
            $logOptions
                ->setDescriptionForEvent(function (string $eventName) {
                    return $this->getActivityDescriptionForEvent($eventName);
                });
        }
        if(method_exists($this, 'getLogNameToUse')){
            $logName = $this->getLogNameToUse();
            if (is_string($logName) || is_null($logName)) {
                $logOptions->useLogName($logName);
            }
        } 
        return $logOptions;
    }

    public function tapActivity(Activity $activity, string $eventName)
    {   
        $activity->user_agent = request()->userAgent();
        $activity->ip_address = request()->ip();
        
        if(method_exists($this, 'getCustomAttributes')){
            $activity->properties = $this->getCustomAttributes($activity);
        } 
    }
}
