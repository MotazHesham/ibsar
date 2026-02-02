<?php

namespace App\Http\Requests\Admin;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBeneficiaryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:' . config('panel.max_characters_short'),
                'required',
            ],
            'email' => [ 
                'unique:users',
                'email',
                'max:' . config('panel.max_characters_short'),
            ], 
            'phone' => [ 
                'required',
                config('panel.phone_validation'),
                'unique:users',
            ],
            'phone_2' => [
                config('panel.phone_validation'),
                'nullable',
            ],
            'password' => [
                'required',
                'min:' . config('panel.password_min_length'),
                'max:' . config('panel.password_max_length'),
            ], 
            'identity_num' => [ 
                'required',
                'unique:users',
                config('panel.identity_validation'),
            ],
        ];
    }
}
