<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\Admin\MassDestroyConsultantRequest;
use App\Http\Requests\Admin\StoreConsultantRequest;
use App\Http\Requests\Admin\UpdateConsultantRequest;
use App\Models\Consultant;
use App\Models\ConsultationType;
use App\Models\ConsultantSchedule;
use App\Models\BeneficiaryOrderAppointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ConsultantsController extends Controller
{
    use MediaUploadingTrait;
    

    public function getAvailableDays(Request $request)
    {
        $consultationTypeId = $request->get('consultation_type_id');
        $attendanceType = $request->get('attendance_type');

        if (!$consultationTypeId || !$attendanceType) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Get consultants with the specified consultation type
        $consultants = Consultant::where('consultation_type_id', $consultationTypeId)->get();
        
        if ($consultants->isEmpty()) {
            return response()->json(['available_days' => []]);
        }

        // Get all available days from consultant schedules
        $availableDays = ConsultantSchedule::whereIn('consultant_id', $consultants->pluck('id'))
            ->where('attendance_type', $attendanceType)
            ->where('is_active', true)
            ->pluck('day')
            ->unique()
            ->values()
            ->toArray();


        return response()->json(['available_days' => $availableDays]);
    }

    public function getAvailableTimes(Request $request)
    {
        $date = $request->get('date');
        $consultationTypeId = $request->get('consultation_type_id');
        $attendanceType = $request->get('attendance_type');

        if (!$date || !$consultationTypeId || !$attendanceType) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $date = Carbon::createFromFormat(config('panel.date_format'), $date)->format('Y-m-d'); 
        // Get the day of week for the selected date
        $dayOfWeek = date('l', strtotime($date)); // Returns: Monday, Tuesday, etc.

        // Get consultants with the specified consultation type
        $consultants = Consultant::where('consultation_type_id', $consultationTypeId)->get();
        
        if ($consultants->isEmpty()) {
            return response()->json(['available_times' => []]);
        }

        // Get consultant schedules for the specific day and attendance type
        $schedules = ConsultantSchedule::whereIn('consultant_id', $consultants->pluck('id'))
            ->where('day', $dayOfWeek)
            ->where('attendance_type', $attendanceType)
            ->where('is_active', true)
            ->get();

        $availableTimes = [];

        foreach ($schedules as $schedule) {
            // Generate time slots based on start_time, end_time, and slot_duration
            $startTime = strtotime($schedule->start_time);
            $endTime = strtotime($schedule->end_time);
            $slotDuration = $schedule->slot_duration * 60; // Convert to seconds

            for ($time = $startTime; $time < $endTime; $time += $slotDuration) {
                $timeSlot = date('H:i', $time);
                
                // Check if this time slot is already booked
                $isBooked = BeneficiaryOrderAppointment::where('consultant_id', $schedule->consultant_id)
                    ->where('date', $date)
                    ->where('time', $timeSlot)
                    ->where('status', 'confirmed')
                    ->exists();

                if (!$isBooked) {
                    $availableTimes[] = [
                        'time' => $timeSlot,
                        'consultant_id' => $schedule->consultant_id,
                        'consultant_name' => $schedule->consultant->name ?? 'Unknown'
                    ];
                }
            }
        }

        // Sort by time
        usort($availableTimes, function($a, $b) {
            return strtotime($a['time']) - strtotime($b['time']);
        });

        return response()->json(['available_times' => $availableTimes]);
    }
} 