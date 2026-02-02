<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Utils\LogsModelActivity;
use Spatie\Activitylog\Models\Activity;

class BeneficiaryOrder extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    public $table = 'beneficiary_orders';

    protected $appends = [
        'attachment',
    ]; 

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const ACCEPT_STATUS_RADIO = [
        'yes' => 'الطلب مقبول',
        'no'  => 'الطلب مرفوض',
    ];

    protected $fillable = [
        'beneficiary_id',
        'beneficiary_family_id',
        'signature',
        'service_type',
        'service_id',
        'title',
        'description',
        'status_id',
        'accept_status',
        'note',
        'refused_reason',
        'done',
        'specialist_id',
        'is_archived',
        'handle',
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

    public function beneficiaryFamily()
    {
        return $this->belongsTo(BeneficiaryFamily::class, 'beneficiary_family_id');
    }

    public function beneficiaryOrderFollowups()
    {
        return $this->hasMany(BeneficiaryOrderFollowup::class, 'beneficiary_order_id', 'id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function courseStudent()
    {
        return $this->hasOne(CourseStudent::class, 'beneficiary_order_id');
    }

    public function beneficiaryOrderAppointment()
    {
        return $this->hasOne(BeneficiaryOrderAppointment::class, 'beneficiary_order_id');
    }

    public function serviceLoan()
    {
        return $this->hasOne(ServiceLoan::class, 'beneficiary_order_id');
    }

    public function dynamicServiceOrder()
    {
        return $this->hasOne(DynamicServiceOrder::class, 'beneficiary_order_id');
    }

    public function getAttachmentAttribute()
    {
        return $this->getMedia('attachment')->last();
    }

    public function status()
    {
        return $this->belongsTo(ServiceStatus::class, 'status_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    } 

    public function serviceKeyName()
    {
        return $this->service ? $this->service->key_name : null;
    }
    
    public function getActivityDescriptionForEvent($eventName){
        if ($eventName == 'created') {
            return 'تم أضافة طلب جديد رقم ' . $this->id;
        } elseif ($eventName == 'updated') {
            return 'تم تحديث بيانات الطلب رقم ' . $this->id;
        } elseif ($eventName == 'deleted') {
            return 'تم حذف بيانات الطلب رقم ' . $this->id;
        }
    } 

    public function getLogNameToUse(): ?string
    {
        return 'beneficiary_order_activity-'.$this->id;
    }
    
    public function getLogAttributes()
    {
        return [ 
            'service_type',
            'title',
            'description',
            'accept_status',
            'note',
            'refused_reason',
            'done',
            'is_archived',

            'service->id',
            'service->name',
            'status->id',
            'status->name',
            'specialist->id',
            'specialist->name',
        ];
    }
    
    public function getCustomAttributes(Activity $activity)
    {   
        $properties = $activity->properties ?? [];
        
        $transformData = function($data) use ($activity) {
            if(isset($data['is_archived'])){
                if($activity->event != 'created'){
                    $data['is_archived'] = $data['is_archived'] == 1 ? 'مؤرشف' : 'غير مؤرشف';
                }
            }
            if(isset($data['accept_status']) && isset(self::ACCEPT_STATUS_RADIO[$data['accept_status']])){ 
                $data['accept_status'] = self::ACCEPT_STATUS_RADIO[$data['accept_status']];
            }
            if(isset($data['done'])){ 
                $data['done'] = $data['done'] == 1 ? 'منتهي' : 'غير منتهي'; 
            }
            if(isset($data['service_type']) && isset(Service::TYPE_SELECT[$data['service_type']])){ 
                $data['service_type'] = Service::TYPE_SELECT[$data['service_type']];
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
