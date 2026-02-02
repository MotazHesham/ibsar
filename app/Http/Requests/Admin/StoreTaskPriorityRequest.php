<?php

namespace App\Http\Requests\Admin;

use App\Models\TaskPriority;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTaskPriorityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_priority_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'badge_class' => [
                'required',
            ],
        ];
    }
}
