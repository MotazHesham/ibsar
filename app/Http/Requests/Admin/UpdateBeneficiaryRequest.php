<?php

namespace App\Http\Requests\Admin;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBeneficiaryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_edit');
    }

    public function rules()
    {
        $step = $this->request->get('step');
        $rules['step'] = [
            'required',
            'in:login_information,basic_information,economic_information,work_information,documents',
        ];
        
        // Use field visibility service to get validation rules
        $fieldVisibilityRules = \App\Services\BeneficiaryFieldVisibilityService::getValidationRules($step);
        $rules = array_merge($rules, $fieldVisibilityRules);
        
        // Add unique constraints for specific fields
        if($step == 'login_information'){
            if(isset($rules['email'])) {
                $rules['email'][] = 'unique:users,email,' . $this->route('beneficiary')->user->id;
            }
            if(isset($rules['phone'])) {
                $rules['phone'][] = 'unique:users,phone,' . $this->route('beneficiary')->user->id;
            }
            if(isset($rules['identity_num'])) {
                $rules['identity_num'][] = 'unique:users,identity_num,' . $this->route('beneficiary')->user->id;
            }
        }

        
        return $rules;
    }
}
