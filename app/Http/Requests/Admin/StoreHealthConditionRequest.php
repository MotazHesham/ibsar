<?php

namespace App\Http\Requests\Admin;

use App\Models\HealthCondition;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreHealthConditionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('health_condition_create');
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
