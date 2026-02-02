<?php

namespace App\Http\Requests\Admin;

use App\Models\HealthCondition;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateHealthConditionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('health_condition_edit');
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
