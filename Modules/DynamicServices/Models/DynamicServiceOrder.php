<?php

namespace Modules\DynamicServices\Models;

use App\Models\BeneficiaryOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DynamicServiceOrder extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia;

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
        'workflow_step',
        'approval_stage',
        'workflow_data',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'field_data' => 'array',
        'workflow_data' => 'array',
        'approval_stage' => 'integer',
    ];

    public function getFieldDataAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

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

    public function getAllFieldData()
    {
        return $this->field_data ?? [];
    }

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

    public function setFieldData($fieldData)
    {
        $this->field_data = $fieldData;
    }

    public function setFieldDataRaw($fieldData)
    {
        $this->attributes['field_data'] = json_encode($fieldData, JSON_UNESCAPED_UNICODE);
    }

    public function setField($fieldId, $value, $metadata = [])
    {
        $fieldData = $this->field_data ?? [];

        $found = false;
        foreach ($fieldData as &$field) {
            if ($field['id'] == $fieldId) {
                $field['value'] = $value;
                $field = array_merge($field, $metadata);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $fieldData[] = array_merge([
                'id' => $fieldId,
                'value' => $value,
            ], $metadata);
        }

        $this->field_data = $fieldData;
    }

    public function getDynamicFieldMedia(int $fieldId)
    {
        return $this->getFirstMedia('dynamic_field_' . $fieldId);
    }
}
