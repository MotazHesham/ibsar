<?php

namespace App\Models;

use App\Models\Traits\HasWorkflowInstance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicServiceOrder extends Model
{
    use HasFactory, HasWorkflowInstance, SoftDeletes;

    public $table = 'dynamic_service_orders';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'beneficiary_order_id',
        'dynamic_service_id',
        'field_data',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'field_data' => 'array',
    ];

    /**
     * Custom accessor for field_data to ensure proper JSON decoding
     */
    public function getFieldDataAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true);
        }
        return $value;
    }

    /**
     * Custom mutator for field_data to ensure proper JSON encoding
     */
    public function setFieldDataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['field_data'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['field_data'] = $value;
        }
    }

    public function beneficiaryOrder()
    {
        return $this->belongsTo(BeneficiaryOrder::class, 'beneficiary_order_id');
    }

    public function dynamicService()
    {
        return $this->belongsTo(DynamicService::class, 'dynamic_service_id');
    }

    public function workflow()
    {
        return $this->hasOne(DynamicServiceWorkflow::class, 'dynamic_service_order_id');
    }

    /**
     * Get a specific field value by field ID
     */
    public function getFieldValue($fieldId)
    {
        if (is_array($this->field_data)) {
            foreach ($this->field_data as $field) {
                if ($field['id'] == $fieldId) {
                    return $field['value'] ?? null;
                }
            }
        }
        return null;
    }

    /**
     * Get a specific field value by field name/label
     */
    public function getFieldValueByName($fieldName)
    {
        if (is_array($this->field_data)) {
            foreach ($this->field_data as $field) {
                if (isset($field['label']) && $field['label'] == $fieldName) {
                    return $field['value'] ?? null;
                }
            }
        }
        return null;
    }

    /**
     * Get all field data with metadata
     */
    public function getAllFieldData()
    {
        return $this->field_data ?? [];
    }

    /**
     * Get only field values (for backward compatibility)
     */
    public function getFieldValues()
    {
        $values = [];
        if (is_array($this->field_data)) {
            foreach ($this->field_data as $field) {
                $values['field_' . $field['id']] = $field['value'] ?? null;
            }
        }
        return $values;
    }

    /**
     * Set field data with metadata
     */
    public function setFieldData($fieldData)
    {
        $this->field_data = $fieldData;
    }

    /**
     * Manually set field data with explicit JSON encoding (useful for bulk operations)
     */
    public function setFieldDataRaw($fieldData)
    {
        $this->attributes['field_data'] = json_encode($fieldData, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Add or update a specific field
     */
    public function setField($fieldId, $value, $metadata = [])
    {
        $fieldData = $this->field_data ?? [];
        
        // Find existing field and update it
        $found = false;
        foreach ($fieldData as &$field) {
            if ($field['id'] == $fieldId) {
                $field['value'] = $value;
                $field = array_merge($field, $metadata);
                $found = true;
                break;
            }
        }
        
        // If field doesn't exist, add it
        if (!$found) {
            $fieldData[] = array_merge([
                'id' => $fieldId,
                'value' => $value
            ], $metadata);
        }
        
        $this->field_data = $fieldData;
    }

}
