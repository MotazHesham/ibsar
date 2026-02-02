<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DynamicService extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'dynamic_services';
    
    protected $appends = [
        'icon',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'category', 
        'service_type',
        'form_fields',
        'program_meetings',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'program_meetings' => 'array',
        'status' => 'string',
    ];

    public const CATEGORY_TRAINING = 'training';
    public const CATEGORY_ASSISTANCE = 'assistance';
    public const CATEGORY_SOCIAL_PROGRAMS = 'social_programs';
    public const CATEGORY_SURGICAL_PROCEDURES = 'surgical_procedures';
    public const CATEGORY_DETECTION_CENTER = 'detection_center';

    public const CATEGORIES = [
        self::CATEGORY_TRAINING => 'التدريب والتأهيل',
        self::CATEGORY_ASSISTANCE => 'مساعدات',
        self::CATEGORY_SOCIAL_PROGRAMS => 'برامج اجتماعية',
        self::CATEGORY_SURGICAL_PROCEDURES => 'الإجراءات الجراحية',
        self::CATEGORY_DETECTION_CENTER => 'مركز الكشف',
    ];

    public function getIconAttribute()
    {
        return $this->getMedia('icon')->last();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getFormFieldsCountAttribute()
    {
        if ($this->form_fields && is_array($this->form_fields)) {
            return count($this->form_fields);
        }
        return 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    } 

    public function beneficiaryOrders()
    {
        return $this->hasMany(BeneficiaryOrder::class, 'service_type', 'id')
            ->where('service_type', 'like', 'dynamic_%');
    }

    public function dynamicServiceOrders()
    {
        return $this->hasMany(DynamicServiceOrder::class, 'dynamic_service_id');
    } 
}
