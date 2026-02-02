<?php

namespace App\Http\Requests\Admin;

use App\Models\DisabilityType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDisabilityTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('disability_type_create');
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
