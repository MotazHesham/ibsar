<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class MassDestroyConsultationTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('consultation_type_delete');
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:consultation_types,id',
        ];
    }
} 