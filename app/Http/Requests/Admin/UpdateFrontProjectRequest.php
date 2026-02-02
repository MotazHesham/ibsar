<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontProject;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateFrontProjectRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('front_project_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'string',
                'required',
            ],
            'description' => [
                'required',
            ],
            'image' => [
                'required',
            ],
        ];
    }
}
