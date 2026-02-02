<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

/**
 * Helper functions for working with Spatie ActivityLog
 */
class ActivityLogHelper
{
    /**
     * Log any model activity with custom description and properties
     *
     * @param Model $model The model instance to log
     * @param string $description The activity description
     * @param array $properties Additional properties to log
     * @param string|null $causerType Type of the causer (default: auth user)
     * @param int|null $causerId ID of the causer (default: auth user)
     * @return Activity
     */
    public static function logModelActivity(
        Model $model,
        string $description,
        array $properties = [],
        ?string $logName = null,
        ?string $event = null,
        ?string $causerType = null,
        ?int $causerId = null
    ): Activity {
        return activity($logName)
            ->performedOn($model)
            ->withProperties($properties)
            ->event($event)
            ->causedBy($causerType ? new $causerType($causerId) : auth()->user())
            ->tap(function (Activity $activity) {
                $activity->user_agent = request()->userAgent();
                $activity->ip_address = request()->ip();
            })
            ->log($description);
    }
}
