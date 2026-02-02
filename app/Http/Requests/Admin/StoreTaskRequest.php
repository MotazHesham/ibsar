<?php

namespace App\Http\Requests\Admin;

use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreTaskRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'status_id' => [
                'required',
                'integer',
            ],
            'tags.*' => [
                'integer',
            ],
            'tags' => [
                'array',
            ],
            'attachment' => [
                'array',
            ],
            'due_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'assigned_tos.*' => [
                'integer',
            ],
            'assigned_tos' => [
                'array',
            ],
            'task_board_id' => [
                'required',
                'integer',
            ],
            'assigned_by_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
