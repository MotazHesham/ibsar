<?php

namespace App\Http\Requests\Beneficiary;


use Illuminate\Foundation\Http\FormRequest; 

class StoreBeneficiaryOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [ 
            'description' => [
                'required',
            ], 
            'service_type' => [
                'required', 
            ], 
        ];
        if($this->service_type == 'consultant'){
            $rules['consultant_id'] = [
                'required',
                'integer',
            ];
            $rules['consultation_type_id'] = [
                'required',
                'integer',
            ];
            $rules['attendance_type'] = [
                'required',
            ];
            $rules['appointment_date'] = [
                'required',
                'date_format:' . config('panel.date_format'),
            ];
            $rules['appointment_time'] = [
                'required',
            ];
        }elseif($this->service_type == 'financial' || $this->service_type == 'social'){
            $rules['service_id'] = [
                'required',
                'integer',
            ];
        }elseif($this->service_type == 'course'){
            $rules['course_id'] = [
                'required',
                'integer',
            ];
            $rules['certificate'] = [
                'required',
            ];
            $rules['transportation'] = [
                'required',
            ];
            $rules['prev_experience'] = [
                'required',
            ];
            $rules['prev_courses'] = [
                'required',
            ];
            $rules['prev_course_name'] = [
                'required_if:prev_courses,1',
            ];
            $rules['prev_course_trainer'] = [
                'required_if:prev_courses,1',
            ];
            $rules['attend_same_course_before'] = [
                'required',
            ];
            $rules['note'] = [
                'required',
            ];
        }elseif($this->service_type == 'loan'){
            $rules['service_id'] = [
                'required',
                'integer',
            ];
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

            $rules['kafil_name'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_identity_num'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_street'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_nearby_address'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_phone'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_phone2'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_work_phone'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_work_address'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_email'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_work_name'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_mail_box'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['kafil_postal_code'] = [ 
                'max:' . config('panel.max_characters_short'),
            ]; 
            $rules['group_name'] = [ 
                'nullable',
                'max:' . config('panel.max_characters_short'),
                'unique:service_loans,group_name',
            ];
            if($this->has('members')){
                $rules['members'] = 'array';
                $rules['members.*.name'] = 'required|string|max:' . config('panel.max_characters_short'); 
                $rules['members.*.identity_number'] = 'required|'. config('panel.identity_validation'); 
            }else if ($this->has('contacts')) {
                $rules['contacts'] = 'array';
                $rules['contacts.*.name'] = 'required|string|max:' . config('panel.max_characters_short');
                $rules['contacts.*.family_relationship_id'] = 'nullable|integer|exists:family_relationships,id';
                $rules['contacts.*.phone'] = 'required|string|max:' . config('panel.max_characters_short');
                $rules['contacts.*.identity_num'] = 'required|string|max:' . config('panel.max_characters_short');
                $rules['contacts.*.address'] = 'nullable|string|max:' . config('panel.max_characters_short');
            }
        } elseif (str_starts_with($this->service_type, 'dynamic_')) {
            // Dynamic service validation
            $dynamicServiceId = \App\Helpers\DynamicServiceHelper::extractDynamicServiceId($this->service_type);
            $dynamicService = \App\Models\DynamicService::find($dynamicServiceId);
            
            // Require training_service_type for training category
            if ($dynamicService && $dynamicService->category === 'training') {
                $rules['training_service_type'] = 'required|in:individual,group';
            }
            
            if ($dynamicService && $dynamicService->form_fields) {
                $formFields = json_decode($dynamicService->form_fields, true);
                foreach ($formFields as $field) {
                    $fieldName = 'dynamic_field_' . $field['id'];
                    $fieldRules = [];
                    
                    if (isset($field['required']) && $field['required']) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }
                    
                    // Add field type validation
                    switch ($field['type'] ?? 'text') {
                        case 'email':
                            $fieldRules[] = 'email';
                            break;
                        case 'number':
                            $fieldRules[] = 'numeric';
                            break;
                        case 'date':
                            $fieldRules[] = 'date';
                            break;
                        case 'url':
                            $fieldRules[] = 'url';
                            break;
                    }
                    
                    // Add custom validation if specified
                    if (isset($field['validation']) && $field['validation']) {
                        $fieldRules[] = $field['validation'];
                    }
                    
                    $rules[$fieldName] = $fieldRules;
                }
            }
        }
        return $rules;
    }
}
