<?php

namespace App\Http\Requests\Beneficiary;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileBeneficiaryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $step = $this->request->get('step');
        $rules['step'] = [
            'required',
            'in:login_information,basic_information,economic_information,work_information,documents,request_join',
        ];
        
        // Use field visibility service to get validation rules
        $fieldVisibilityRules = \App\Services\BeneficiaryFieldVisibilityService::getValidationRules($step);
        $rules = array_merge($rules, $fieldVisibilityRules);
        
        // Add unique constraints for specific fields
        if($step == 'login_information'){
            if(isset($rules['email'])) {
                $rules['email'][] = 'unique:users,email,' . $this->user_id;
            }
            if(isset($rules['phone'])) {
                $rules['phone'][] = 'unique:users,phone,' . $this->user_id;
            }
            if(isset($rules['identity_num'])) {
                $rules['identity_num'][] = 'unique:users,identity_num,' . $this->user_id;
            }
        }

        
        return $rules;
    }
}
