<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateConsultantRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('consultant_edit');
    }

    public function rules()
    {
        return [
            'consultation_type_id' => [
                'required',
                'integer',
                'exists:consultation_types,id',
            ],
            'name' => [
                'string',
                'max:255',
                'required',
            ],
            'national_id' => [
                'string',
                'max:255',
                'required',
            ],
            'phone_number' => [
                'string',
                'max:255',
                'required',
            ],
            'academic_degree' => [
                'string',
                'max:255',
                'required',
            ],
            'documents' => [
                'array',
            ], 
        ];
    }
} 