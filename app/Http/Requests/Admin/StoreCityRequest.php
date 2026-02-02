<?php

namespace App\Http\Requests\Admin;

use App\Models\City;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreCityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('city_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'districts.*' => [
                'integer',
            ],
            'districts' => [
                'array',
            ],
        ];
    }
}
