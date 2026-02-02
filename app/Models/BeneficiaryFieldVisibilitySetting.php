<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryFieldVisibilitySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_name',
        'field_group',
        'field_label',
        'is_visible',
        'is_required',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_required' => 'boolean',
    ];

    /**
     * Get all visible fields for a specific group
     */
    public static function getVisibleFieldsForGroup($group)
    {
        return static::where('field_group', $group)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all required fields for a specific group
     */
    public static function getRequiredFieldsForGroup($group)
    {
        return static::where('field_group', $group)
            ->where('is_visible', true)
            ->where('is_required', true)
            ->pluck('field_name')
            ->toArray();
    }

    /**
     * Check if a field is visible
     */
    public static function isFieldVisible($fieldName)
    {
        $field = static::where('field_name', $fieldName)->first();
        return $field ? $field->is_visible : true; // Default to visible if not found
    }

    /**
     * Check if a field is required
     */
    public static function isFieldRequired($fieldName)
    {
        $field = static::where('field_name', $fieldName)->first();
        return $field ? $field->is_required : false; // Default to not required if not found
    }

    /**
     * Get field settings by group
     */
    public static function getFieldsByGroup($group)
    {
        return static::where('field_group', $group)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all field groups
     */
    public static function getFieldGroups()
    {
        return static::select('field_group')
            ->distinct()
            ->orderBy('field_group')
            ->pluck('field_group')
            ->toArray();
    }
}