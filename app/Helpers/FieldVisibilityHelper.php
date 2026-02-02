<?php

namespace App\Helpers;

use App\Services\BeneficiaryFieldVisibilityService;

class FieldVisibilityHelper
{
    /**
     * Check if a field should be displayed
     */
    public static function shouldShowField($fieldName)
    {
        return BeneficiaryFieldVisibilityService::shouldShowField($fieldName);
    }

    /**
     * Check if a field is required
     */
    public static function isFieldRequired($fieldName)
    {
        return BeneficiaryFieldVisibilityService::isFieldRequired($fieldName);
    }

    /**
     * Get field configuration
     */
    public static function getFieldConfig($fieldName)
    {
        return BeneficiaryFieldVisibilityService::getFieldConfig($fieldName);
    }

    /**
     * Render field with visibility check
     */
    public static function renderField($fieldName, $fieldConfig, $additionalParams = [])
    {
        if (!self::shouldShowField($fieldName)) {
            return '';
        }

        $config = self::getFieldConfig($fieldName);
        $isRequired = $config['is_required'] || ($additionalParams['isRequired'] ?? false);
        
        // Merge additional parameters with field config
        $params = array_merge($additionalParams, [
            'isRequired' => $isRequired,
            'label' => $config['field_label'] ?? $additionalParams['label'] ?? ucfirst(str_replace('_', ' ', $fieldName)),
        ]);

        return $params;
    }
}
