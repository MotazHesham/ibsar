<?php

namespace App\Http\Requests\Beneficiary;


use Illuminate\Foundation\Http\FormRequest; 

class UpdateLoanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules['street'] = [ 
            'max:' . config('panel.max_characters_short'),
        ];
        $rules['project_start_date'] = [ 
            'date_format:' . config('panel.date_format'),
        ];
        $rules['project_years_of_experience'] = [
            'max:' . config('panel.max_characters_short'),
        ];
        $rules['loan_id'] = [
            'required',
            'integer',
        ];
        $rules['project_short_description'] = [ 
            'max:' . config('panel.max_characters_short'),
        ]; 
        $rules['project_short_description'] = [ 
            'max:' . config('panel.max_characters_short'),
        ]; 
        $rules['purpose_of_loan'] = [ 
            'max:' . config('panel.max_characters_short'),
        ]; 
        $rules['previous_loan_number'] = [ 
            'max:' . config('panel.max_characters_short'),
        ]; 
        return $rules;
    }
}
