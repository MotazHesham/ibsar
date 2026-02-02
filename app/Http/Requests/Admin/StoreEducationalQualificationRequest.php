<?php

namespace App\Http\Requests\Admin;

use App\Models\EducationalQualification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreEducationalQualificationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('educational_qualification_create');
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
