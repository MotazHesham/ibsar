<?php

namespace App\Helpers;

use App\Models\DynamicService;
use App\Models\DynamicServiceOrder;

class DynamicServiceHelper
{
    /**
     * Get dynamic service title from service type
     */
    public static function getServiceTitle($serviceType)
    {
        if (str_starts_with($serviceType, 'dynamic_')) {
            $dynamicServiceId = str_replace('dynamic_', '', $serviceType);
            $dynamicService = DynamicService::find($dynamicServiceId);
            return $dynamicService ? $dynamicService->title : 'Dynamic Service';
        }
        return $serviceType;
    }

    /**
     * Get dynamic service from service type
     */
    public static function getDynamicService($serviceType)
    {
        if (str_starts_with($serviceType, 'dynamic_')) {
            $dynamicServiceId = str_replace('dynamic_', '', $serviceType);
            return DynamicService::find($dynamicServiceId);
        }
        return null;
    }

    /**
     * Get dynamic service order data
     */
    public static function getDynamicServiceOrder($beneficiaryOrderId)
    {
        return DynamicServiceOrder::where('beneficiary_order_id', $beneficiaryOrderId)->first();
    }

    /**
     * Get field value from dynamic service order by field ID
     */
    public static function getFieldValue($beneficiaryOrderId, $fieldId)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getFieldValue($fieldId);
        }
        return null;
    }

    /**
     * Get field value from dynamic service order by field label/name
     */
    public static function getFieldValueByName($beneficiaryOrderId, $fieldName)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getFieldValueByName($fieldName);
        }
        return null;
    }

    /**
     * Get all field data with metadata
     */
    public static function getAllFieldData($beneficiaryOrderId)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getAllFieldData();
        }
        return [];
    }

    /**
     * Get field values in old format (for backward compatibility)
     */
    public static function getFieldValues($beneficiaryOrderId)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getFieldValues();
        }
        return [];
    }

    /**
     * Check if service type is dynamic
     */
    public static function isDynamicService($serviceType)
    {
        return str_starts_with($serviceType, 'dynamic_');
    }

    /**
     * Extract dynamic service ID from service type
     */
    public static function extractDynamicServiceId($serviceType)
    {
        if (self::isDynamicService($serviceType)) {
            return str_replace('dynamic_', '', $serviceType);
        }
        return null;
    }

    /**
     * Format field data for display
     */
    public static function formatFieldDataForDisplay($fieldData)
    {
        $formatted = [];
        foreach ($fieldData as $field) {
            $formatted[] = [
                'label' => $field['label'] ?? 'Field ' . $field['id'],
                'value' => $field['value'] ?? '',
                'type' => $field['type'] ?? 'text',
                'required' => $field['required'] ?? false,
            ];
        }
        return $formatted;
    }
}
