<?php

namespace App\Http\Requests\Admin;

use App\Models\JobType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateJobTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('job_type_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
            ],
        ];
    }
}
