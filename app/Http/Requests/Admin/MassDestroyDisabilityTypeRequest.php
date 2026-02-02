<?php

namespace App\Http\Requests\Admin;

use App\Models\DisabilityType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyDisabilityTypeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('disability_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:disability_types,id',
        ];
    }
}
