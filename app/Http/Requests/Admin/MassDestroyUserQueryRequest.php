<?php

namespace App\Http\Requests\Admin;

use App\Models\UserQuery;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyUserQueryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('user_query_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:user_queries,id',
        ];
    }
}
