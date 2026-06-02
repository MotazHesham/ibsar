<?php

namespace Modules\DynamicServices\Helpers;

class DynamicServiceViewHelper
{
    public static function renderFieldsTable($fieldData)
    {
        if (empty($fieldData)) {
            return '<p class="text-muted">No dynamic fields found.</p>';
        }

        $html = '<table class="table table-bordered table-striped">';
        $html .= '<thead><tr><th>Field</th><th>Value</th></tr></thead><tbody>';

        foreach ($fieldData as $field) {
            $label = $field['label'] ?? 'Field ' . $field['id'];
            $value = $field['value'] ?? '';
            $type = $field['type'] ?? 'text';
            $formattedValue = self::formatFieldValue($value, $type, $field);

            $html .= '<tr>';
            $html .= '<td><strong>' . htmlspecialchars($label) . '</strong></td>';
            $html .= '<td>' . $formattedValue . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    public static function renderFieldsCard($fieldData)
    {
        if (empty($fieldData)) {
            return '<div class="alert alert-info">No dynamic fields found.</div>';
        }

        $html = '<div class="row">';

        foreach ($fieldData as $field) {
            $label = $field['label'] ?? 'Field ' . $field['id'];
            $value = $field['value'] ?? '';
            $type = $field['type'] ?? 'text';
            $required = $field['required'] ?? false;
            $formattedValue = self::formatFieldValue($value, $type, $field);

            $html .= '<div class="col-md-6 mb-3">';
            $html .= '<div class="card">';
            $html .= '<div class="card-header">';
            $html .= '<h6 class="mb-0">' . htmlspecialchars($label);
            if ($required) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</h6>';
            $html .= '</div>';
            $html .= '<div class="card-body">';
            $html .= $formattedValue;
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public static function getFieldDisplayValue(array $field): mixed
    {
        $value = $field['value'] ?? null;

        if (in_array($field['type'] ?? 'text', ['select', 'radio', 'checkbox'], true) && ! empty($field['options'])) {
            if (is_numeric($value)) {
                $index = (int) $value;
                if (isset($field['options'][$index])) {
                    return $field['options'][$index];
                }
            }
        }

        return $value;
    }

    public static function formatFieldValue($value, $type, array $field = [])
    {
        if (empty($value)) {
            return '<span class="text-muted">Not provided</span>';
        }

        switch ($type) {
            case 'email':
                return '<a href="mailto:' . htmlspecialchars($value) . '">' . htmlspecialchars($value) . '</a>';
            case 'url':
                return '<a href="' . htmlspecialchars($value) . '" target="_blank">' . htmlspecialchars($value) . '</a>';
            case 'date':
                return '<span class="badge bg-info">' . htmlspecialchars($value) . '</span>';
            case 'number':
                return '<span class="badge bg-secondary">' . htmlspecialchars($value) . '</span>';
            case 'textarea':
                return '<div class="text-wrap">' . nl2br(htmlspecialchars($value)) . '</div>';
            case 'select':
            case 'radio':
            case 'checkbox':
                return '<span class="badge bg-primary">' . htmlspecialchars($value) . '</span>';
            case 'file':
                $displayName = $field['original_name'] ?? $field['file_name'] ?? basename(parse_url((string) $value, PHP_URL_PATH) ?: '');

                return '<a href="' . htmlspecialchars((string) $value) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-attachment-line me-1"></i>' . htmlspecialchars($displayName) . '</a>';
            default:
                return htmlspecialchars($value);
        }
    }

    public static function getFieldSummary($fieldData, $maxFields = 3)
    {
        if (empty($fieldData)) {
            return 'No dynamic fields';
        }

        $summary = [];
        $count = 0;

        foreach ($fieldData as $field) {
            if ($count >= $maxFields) {
                break;
            }

            $label = $field['label'] ?? 'Field ' . $field['id'];
            $value = $field['value'] ?? '';

            if (!empty($value)) {
                $summary[] = $label . ': ' . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value);
                $count++;
            }
        }

        if (empty($summary)) {
            return 'No values provided';
        }

        return implode(', ', $summary);
    }
}
