<?php

namespace App\Http\Requests\Admin;

use App\Models\VolunteerTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreVolunteerTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('volunteer_task_create');
    }

    public function rules()
    {
        return [
            'volunteer_id' => ['required', 'exists:volunteers,id'],
            'name'        => ['required', 'string', 'max:255'],
            'identity'    => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string'],
            'phone'       => ['required', 'string', 'max:255'],
            'details'     => ['nullable', 'string'],
            'visit_type'  => ['required', 'in:' . implode(',', array_keys(VolunteerTask::VISIT_TYPE_SELECT))],
            'date'        => ['required', 'date_format:' . config('panel.date_format')],
            'arrive_time' => ['nullable', 'date_format:H:i'],
            'leave_time'  => ['nullable', 'date_format:H:i'],
            'status'      => ['nullable', 'in:' . implode(',', array_keys(VolunteerTask::STATUS_SELECT))],
            'cancel_reason' => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
        ];
    }
}
