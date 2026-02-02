<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDynamicServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $data = [
            'title' => [
                'string',
                'required',
                'max:255',
            ],
            'slug' => [
                'string',
                'required',
                'max:255',
                Rule::unique('dynamic_services', 'slug')->ignore($this->dynamic_service->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ], 
            'icon' => [
                'nullable',
                'string',
                'max:255',
            ],
            'form_fields' => [
                'nullable',
                'json',
            ],
        ];

        if ($this->has('form_fields')) {
            $formFields = $this->form_fields;
            // If it's a string, decode it. If it's already an array, use it directly
            if (is_string($formFields)) {
                $formFields = json_decode($formFields, true);
            }
            if (is_array($formFields)) {
                foreach ($formFields as $field) {
                    validator($field, [
                        'label' => 'required|string|max:255',
                        'type' => 'required|in:text,textarea,select,radio,checkbox,date,time,number',
                        'required' => 'boolean',
                        'grid' => 'nullable|string|in:col-md-3,col-md-4,col-md-6,col-md-12',
                        'options' => 'nullable|array',
                    ])->validate();
                }
            }
        }

        return $data;
    }
}
