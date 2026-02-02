<?php

namespace App\Http\Requests\Admin;

use App\Models\Section;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSectionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('section_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'department_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
