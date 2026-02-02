<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateConsultantScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('consultant_schedule_edit');
    }

    public function rules()
    {
        return [
            'consultant_id' => [
                'required',
                'integer',
                'exists:consultants,id',
            ],
            'day' => [
                'required',
                'in:' . implode(',', array_keys(\App\Models\ConsultantSchedule::DAY_SELECT)),
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],
            'slot_duration' => [
                'required',
                'integer',
                'min:15',
                'max:480', // 8 hours max
            ],
            'attendance_type' => [
                'required',
                'in:' . implode(',', array_keys(\App\Models\ConsultantSchedule::ATTENDANCE_TYPE_SELECT)),
            ],
        ];
    }
} 