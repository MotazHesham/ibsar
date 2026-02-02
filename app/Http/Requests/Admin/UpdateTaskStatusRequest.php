<?php

namespace App\Http\Requests\Admin;

use App\Models\TaskStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_status_edit');
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
            'ordering' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
