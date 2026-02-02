<?php

namespace App\Observers;

use App\Models\Consultant;
use App\Models\ConsultantSchedule;

class ConsultantObserver
{
    /**
     * Handle the Consultant "created" event.
     */
    public function created(Consultant $consultant): void
    {
        // Create default schedules for all days of the week
        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday', 
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];

        foreach ($days as $dayKey => $dayName) {
            ConsultantSchedule::create([
                'consultant_id' => $consultant->id,
                'day' => $dayKey,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'slot_duration' => 30,
                'attendance_type' => 'in_person',
                'is_active' => false, // Default to inactive, admin can enable specific days
            ]);
        }
    }

    /**
     * Handle the Consultant "updated" event.
     */
    public function updated(Consultant $consultant): void
    {
        //
    }

    /**
     * Handle the Consultant "deleted" event.
     */
    public function deleted(Consultant $consultant): void
    {
        // Delete all associated schedules when consultant is deleted
        $consultant->schedules()->delete();
    }

    /**
     * Handle the Consultant "restored" event.
     */
    public function restored(Consultant $consultant): void
    {
        //
    }

    /**
     * Handle the Consultant "force deleted" event.
     */
    public function forceDeleted(Consultant $consultant): void
    {
        // Delete all associated schedules when consultant is force deleted
        $consultant->schedules()->forceDelete();
    }
} 