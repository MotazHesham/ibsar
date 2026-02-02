<?php

namespace App\Http\Requests\Admin;

use App\Models\Nationality;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyNationalityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('nationality_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:nationalities,id',
        ];
    }
}
