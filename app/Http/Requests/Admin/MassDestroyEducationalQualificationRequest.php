<?php

namespace App\Http\Requests\Admin;

use App\Models\EducationalQualification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyEducationalQualificationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('educational_qualification_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:educational_qualifications,id',
        ];
    }
}
