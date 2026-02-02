<?php

namespace App\Http\Requests\Beneficiary;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\FieldVisibilityHelper;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => [
                'string',
                'max:' . config('panel.max_characters_short'),
                'required',
            ], 
            'phone' => [ 
                'required',
                config('panel.phone_validation'),
                'unique:users',
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

        // Add beneficiary_category_id validation if field is visible
        if (FieldVisibilityHelper::shouldShowField('beneficiary_category_id')) {
            $rules['beneficiary_category_id'] = [
                'exists:beneficiary_categories,id',
            ];
            
            if (FieldVisibilityHelper::isFieldRequired('beneficiary_category_id')) {
                $rules['beneficiary_category_id'][] = 'required';
            } else {
                $rules['beneficiary_category_id'][] = 'nullable';
            }
        }

        return $rules;
    }
}
