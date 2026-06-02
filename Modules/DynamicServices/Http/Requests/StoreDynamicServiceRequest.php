<?php

namespace Modules\DynamicServices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\DynamicServices\Models\DynamicService;

class StoreDynamicServiceRequest extends FormRequest
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
                'unique:dynamic_services,slug',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
            'category' => [
                'required',
                'in:training,assistance,social_programs,surgical_procedures,detection_center',
            ],
            'target_count' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'service_type' => [
                'nullable',
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
            if (is_string($formFields)) {
                $formFields = json_decode($formFields, true);
            }
            if (is_array($formFields)) {
                foreach ($formFields as $field) {
                    validator($field, [
                        'label' => 'required|string|max:255',
                        'type' => 'required|in:text,textarea,select,radio,checkbox,date,time,number,file',
                        'required' => 'boolean',
                        'grid' => 'nullable|string|in:col-md-3,col-md-4,col-md-6,col-md-12',
                        'options' => 'nullable|array',
                    ])->validate();
                }
            }
        }

        if ($this->category === DynamicService::CATEGORY_SOCIAL_PROGRAMS) {
            $data['target_count'] = ['required', 'integer', 'min:1'];
            unset($data['service_type']);
        } elseif ($this->category === DynamicService::CATEGORY_TRAINING) {
            $data['service_type'] = ['nullable', 'in:individual,group'];
        } elseif ($this->category === DynamicService::CATEGORY_ASSISTANCE) {
            $data['service_type'] = ['required', 'in:in_kind,financial'];
        } else {
            unset($data['service_type'], $data['target_count']);
        }

        return $data;
    }

    protected function prepareForValidation(): void
    {
        if ($this->category === 'social_programs' && $this->filled('target_count')) {
            $this->merge(['service_type' => (string) $this->input('target_count')]);
        }
    }
}
