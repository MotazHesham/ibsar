<?php

namespace Modules\DynamicServices\Helpers;

use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;

class DynamicServiceHelper
{
    public static function getServiceTitle($serviceType)
    {
        if (str_starts_with($serviceType, 'dynamic_')) {
            $dynamicServiceId = str_replace('dynamic_', '', $serviceType);
            $dynamicService = DynamicService::find($dynamicServiceId);

            return $dynamicService ? $dynamicService->title : 'Dynamic Service';
        }

        return $serviceType;
    }

    public static function getDynamicService($serviceType)
    {
        if (str_starts_with($serviceType, 'dynamic_')) {
            $dynamicServiceId = str_replace('dynamic_', '', $serviceType);

            return DynamicService::find($dynamicServiceId);
        }

        return null;
    }

    public static function getDynamicServiceOrder($beneficiaryOrderId)
    {
        return DynamicServiceOrder::where('beneficiary_order_id', $beneficiaryOrderId)->first();
    }

    public static function getFieldValue($beneficiaryOrderId, $fieldId)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getFieldValue($fieldId);
        }

        return null;
    }

    public static function getFieldValueByName($beneficiaryOrderId, $fieldName)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getFieldValueByName($fieldName);
        }

        return null;
    }

    public static function getAllFieldData($beneficiaryOrderId)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getAllFieldData();
        }

        return [];
    }

    public static function getFieldValues($beneficiaryOrderId)
    {
        $dynamicServiceOrder = self::getDynamicServiceOrder($beneficiaryOrderId);
        if ($dynamicServiceOrder) {
            return $dynamicServiceOrder->getFieldValues();
        }

        return [];
    }

    public static function isDynamicService($serviceType)
    {
        return str_starts_with($serviceType, 'dynamic_');
    }

    public static function getServiceIconUrl(?string $serviceType): string
    {
        if (! $serviceType) {
            return asset('assets/images/services/dynamic.png');
        }

        if (self::isDynamicService($serviceType)) {
            $dynamicService = self::getDynamicService($serviceType);
            $iconMedia = $dynamicService?->getFirstMedia('icon');

            if ($iconMedia) {
                return $iconMedia->getUrl();
            }

            return asset('assets/images/services/dynamic.png');
        }

        return asset('assets/images/services/' . $serviceType . '.png');
    }

    public static function extractDynamicServiceId($serviceType)
    {
        if (self::isDynamicService($serviceType)) {
            return str_replace('dynamic_', '', $serviceType);
        }

        return null;
    }

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
