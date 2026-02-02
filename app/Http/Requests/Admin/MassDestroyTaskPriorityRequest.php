<?php

namespace App\Http\Requests\Admin;

use App\Models\TaskPriority;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTaskPriorityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('task_priority_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:task_priorities,id',
        ];
    }
}
