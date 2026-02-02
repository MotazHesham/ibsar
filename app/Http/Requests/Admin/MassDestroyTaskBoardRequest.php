<?php

namespace App\Http\Requests\Admin;

use App\Models\TaskBoard;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyTaskBoardRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('task_board_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:task_boards,id',
        ];
    }
}
