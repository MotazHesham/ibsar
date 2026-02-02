<?php

namespace App\Helpers;

class DynamicServiceViewHelper
{
    /**
     * Render dynamic service fields in a table format
     */
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
            
            // Format value based on type
            $formattedValue = self::formatFieldValue($value, $type);
            
            $html .= '<tr>';
            $html .= '<td><strong>' . htmlspecialchars($label) . '</strong></td>';
            $html .= '<td>' . $formattedValue . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Render dynamic service fields in a card format
     */
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
            
            // Format value based on type
            $formattedValue = self::formatFieldValue($value, $type);
            
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

    /**
     * Format field value based on type
     */
    private static function formatFieldValue($value, $type)
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
            
            default:
                return htmlspecialchars($value);
        }
    }

    /**
     * Get field summary for display in lists
     */
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
