<?php

namespace App\Services;

use App\Models\BeneficiaryFieldVisibilitySetting;

class BeneficiaryFieldVisibilityService
{
    public static function getFieldsByGroup($group)
    {
        return BeneficiaryFieldVisibilitySetting::getFieldsByGroup($group);
    }
    /**
     * Get visible fields for a specific group
     */
    public static function getVisibleFields($group)
    {
        return BeneficiaryFieldVisibilitySetting::getVisibleFieldsForGroup($group);
    }

    /**
     * Get required fields for a specific group
     */
    public static function getRequiredFields($group)
    {
        return BeneficiaryFieldVisibilitySetting::getRequiredFieldsForGroup($group);
    }

    /**
     * Check if a field should be displayed
     */
    public static function shouldShowField($fieldName)
    {
        return BeneficiaryFieldVisibilitySetting::isFieldVisible($fieldName);
    }

    /**
     * Check if a field is required
     */
    public static function isFieldRequired($fieldName)
    {
        return BeneficiaryFieldVisibilitySetting::isFieldRequired($fieldName);
    }

    /**
     * Get validation rules for a group based on field visibility settings
     */
    public static function getValidationRules($group)
    {
        $rules = [];
        $requiredFields = self::getRequiredFields($group);

        // Define base validation rules for each field
        $baseRules = [
            'name' => ['string', 'max:' . config('panel.max_characters_short')],
            'email' => ['nullable', 'email', 'max:' . config('panel.max_characters_short')],
            'phone' => [config('panel.phone_validation')],
            'phone_2' => [config('panel.phone_validation'), 'nullable'],
            'identity_num' => [config('panel.identity_validation')],
            'password' => ['nullable', 'min:' . config('panel.password_min_length'), 'max:' . config('panel.password_max_length')],
            'dob' => ['date_format:' . config('panel.date_format'), 'nullable'],
            'address' => ['max:' . config('panel.max_characters_long'), 'nullable'],
            'street' => ['string', 'max:' . config('panel.max_characters_short'), 'nullable'],
            'building_number' => ['string', 'max:4', 'nullable'],
            'building_additional_number' => ['string', 'max:4', 'nullable'],
            'postal_code' => ['string', 'max:5', 'nullable'],
            'custom_disability_type' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'custom_health_condition' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'company_name' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'job_title' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'job_phone' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'job_address' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'expenses.*' => ['nullable', 'max:' . config('panel.max_characters_short')],
            'expenses' => ['array', 'nullable'],
            'incomes' => ['array', 'nullable'],
            'incomes.*' => ['nullable', 'max:' . config('panel.max_characters_short')],
        ];

        // Get all visible fields for the group
        $visibleFields = BeneficiaryFieldVisibilitySetting::getFieldsByGroup($group);
        
        foreach ($visibleFields as $field) {
            if ($field->is_visible) {
                $fieldName = $field->field_name;
                
                // Get base rules for this field
                $fieldRules = $baseRules[$fieldName] ?? ['nullable'];
                
                // Add required rule if field is marked as required
                if ($field->is_required) {
                    $fieldRules = array_merge(['required'], $fieldRules);
                } else {
                    $fieldRules = array_merge(['nullable'], $fieldRules);
                }
                
                $rules[$fieldName] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Get field configuration for form rendering
     */
    public static function getFieldConfig($fieldName)
    {
        $field = BeneficiaryFieldVisibilitySetting::where('field_name', $fieldName)->first();
        
        if (!$field) {
            return [
                'is_visible' => true,
                'is_required' => false,
                'field_label' => ucfirst(str_replace('_', ' ', $fieldName)),
            ];
        }

        return [
            'is_visible' => $field->is_visible,
            'is_required' => $field->is_required,
            'field_label' => $field->field_label,
            'description' => $field->description,
        ];
    }

    /**
     * Get all field groups with their visible fields
     */
    public static function getFieldGroupsWithFields()
    {
        $groups = BeneficiaryFieldVisibilitySetting::getFieldGroups();
        $result = [];

        foreach ($groups as $group) {
            $result[$group] = self::getFieldsByGroup($group);
        }

        return $result;
    }
}
