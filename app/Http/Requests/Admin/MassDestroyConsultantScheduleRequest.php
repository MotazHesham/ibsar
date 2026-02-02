<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class MassDestroyConsultantScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('consultant_schedule_delete');
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:consultant_schedules,id',
        ];
    }
} 