<?php

namespace App\Http\Requests\Admin;

use App\Models\LettersOrganization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyLettersOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('letters_organization_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:letters_organizations,id',
        ];
    }
}
