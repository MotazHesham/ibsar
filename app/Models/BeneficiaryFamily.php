<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Utils\LogsModelActivity; 
use Spatie\Activitylog\Models\Activity;

class BeneficiaryFamily extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    protected $appends = [
        'photo',
    ];

    public $table = 'beneficiary_families';

    public const CAN_WORK_SELECT = [
        'yes' => 'نعم',
        'no'  => 'لا',
    ];

    public const GENDER_SELECT = [
        'male'   => 'ذكر',
        'female' => 'انثى',
    ];

    protected $dates = [
        'dob',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'beneficiary_id',
        'name',
        'gender',
        'dob',
        'phone',
        'email',
        'identity_num',
        'educational_qualification_id',
        'family_relationship_id',
        'marital_status_id',
        'health_condition_id',
        'custom_health_condition',
        'disability_type_id',
        'custom_disability_type',
        'can_work',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function getDobAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function family_relationship()
    {
        return $this->belongsTo(FamilyRelationship::class, 'family_relationship_id');
    }

    public function educational_qualification()
    {
        return $this->belongsTo(EducationalQualification::class, 'educational_qualification_id');
    }

    public function marital_status()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }

    public function health_condition()
    {
        return $this->belongsTo(HealthCondition::class, 'health_condition_id');
    }

    public function disability_type()
    {
        return $this->belongsTo(DisabilityType::class, 'disability_type_id');
    }

    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    } 

    public function getLogAttributes()
    {
        return [
            'id', 'name','gender','dob','phone','email','identity_num',
            'educational_qualification->id',
            'educational_qualification->name',
            'family_relationship->id',
            'family_relationship->name',
            'marital_status->id',
            'marital_status->name',
            'health_condition->id',
            'health_condition->name',
            'custom_health_condition',
            'disability_type->id',
            'disability_type->name',
            'custom_disability_type',
            'can_work',
        ];
    }

    public function getActivityDescriptionForEvent($eventName){
        if ($eventName == 'created') {
            return 'تم أضافة فرد عائلة جديد';
        } elseif ($eventName == 'updated') {
            $description = "تم تحديث بيانات "
                . "<span class='badge bg-info-transparent mb-3'>" . $this->name . "</span> فرد عائلة";
            return $description;
        } elseif ($eventName == 'deleted') {
            return 'تم حذف بيانات فرد عائلة';
        }
    } 
    public function getLogNameToUse(): ?string
    {
        return 'beneficiary_activity-'.$this->beneficiary_id;
    }
    public function getCustomAttributes(Activity $activity)
    {   
        $properties = $activity->properties ?? [];
        
        $transformData = function($data) {
            if (isset($data['gender']) && isset(self::GENDER_SELECT[$data['gender']])) {
                $data['gender'] = self::GENDER_SELECT[$data['gender']];
            }
            if (isset($data['can_work']) && isset(self::CAN_WORK_SELECT[$data['can_work']])) {
                $data['can_work'] = self::CAN_WORK_SELECT[$data['can_work']];
            }
            return $data;
        }; 

        if (isset($properties['attributes'])) {
            $properties['attributes'] = $transformData($properties['attributes']); 
        }
        
        if (isset($properties['old'])) {
            $properties['old'] = $transformData($properties['old']); 
        }
        
        return $properties;
    }
}
