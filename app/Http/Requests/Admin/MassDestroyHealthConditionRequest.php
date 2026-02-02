<?php

namespace App\Http\Requests\Admin;

use App\Models\HealthCondition;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyHealthConditionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('health_condition_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:health_conditions,id',
        ];
    }
}
