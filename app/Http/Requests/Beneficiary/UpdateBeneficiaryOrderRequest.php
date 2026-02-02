<?php

namespace App\Http\Requests\Beneficiary;

use Illuminate\Foundation\Http\FormRequest; 

class UpdateBeneficiaryOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:' . config('panel.max_characters_short'),
            ],
            'description' => [
                'required', 
                'max:' . config('panel.max_characters_long'),
            ], 
        ];
    }
}
