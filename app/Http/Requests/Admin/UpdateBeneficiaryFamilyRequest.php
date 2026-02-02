<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryFamily;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBeneficiaryFamilyRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_family_edit');
    }

    public function rules()
    {
        return [ 
            'photo' => [
                'nullable', 
            ],
            'identity_num' => [
                'required',
                config('panel.identity_validation'),
            ],
            'name' => [
                'string',
                'max:' . config('panel.max_characters_short'),
                'required',
            ],
            'dob' => [
                'date_format:' . config('panel.date_format'),
                'required',
            ],
            'phone' => [ 
                'required',
                config('panel.phone_validation'),
            ],
            'email' => [
                'string',
                'email',
                'max:' . config('panel.max_characters_short'),
                'required',
            ],
            'family_relationship_id' => [
                'required',
                'integer',
            ],
            'marital_status_id' => [    
                'required',
                'integer',
            ],
            'educational_qualification_id' => [
                'required',
                'integer',
            ],
            'health_condition_id' => [
                'required_if:has_health_condition,1', 
            ],
            'custom_health_condition' => [ 
                'nullable',
                'max:' . config('panel.max_characters_long'),
            ],
            'disability_type_id' => [
                'required_if:has_disability,1', 
            ],
            'custom_disability_type' => [ 
                'nullable',
                'max:' . config('panel.max_characters_long'),
            ],
        ];
    }
}
