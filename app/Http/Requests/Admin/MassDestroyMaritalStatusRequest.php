<?php

namespace App\Http\Requests\Admin;

use App\Models\MaritalStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyMaritalStatusRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('marital_status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:marital_statuses,id',
        ];
    }
}
