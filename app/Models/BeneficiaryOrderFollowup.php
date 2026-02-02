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
use Carbon\Carbon;

class BeneficiaryOrderFollowup extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    protected $appends = [
        'attachments',
        'date',
    ];

    public $table = 'beneficiary_order_followups';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'beneficiary_order_id',
        'comment',
        'date',
        'user_id',
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

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
    public function beneficiary_order()
    {
        return $this->belongsTo(BeneficiaryOrder::class, 'beneficiary_order_id');
    }

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    } 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getActivityDescriptionForEvent($eventName){
        if ($eventName == 'created') {
            return 'تم أضافة متابعة رقم ' . $this->id . ' للطلب رقم ' . $this->beneficiary_order_id;
        } elseif ($eventName == 'updated') {
            return 'تم تحديث متابعة رقم ' . $this->id . ' للطلب رقم ' . $this->beneficiary_order_id;
        } elseif ($eventName == 'deleted') {
            return 'تم حذف متابعة رقم ' . $this->id . ' للطلب رقم ' . $this->beneficiary_order_id;
        }
    } 

    public function getLogNameToUse(): ?string
    {
        return 'beneficiary_order_activity-'.$this->beneficiary_order_id;
    }
    
    public function getLogAttributes()
    {
        return [   
            'comment',
            'date', 
            'beneficiary_order->id',
            'beneficiary_order->title',
            'user->id',
            'user->name',
        ];
    }
}
