<?php

namespace App\Http\Requests\Admin;

use App\Models\TaskBoard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTaskBoardRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('task_board_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
