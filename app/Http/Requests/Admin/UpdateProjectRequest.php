<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateProjectRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('project_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:' . config('panel.max_characters_short'),
                'required',
            ],
            'description' => [
                'nullable',
            ],
            'target_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}

