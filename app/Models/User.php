<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Utils\LogsModelActivity;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable implements HasMedia
{
    use SoftDeletes, Notifiable, HasFactory, InteractsWithMedia;
    use LogsModelActivity;

    public $table = 'users';

    protected $appends = [
        'photo',
    ];

    protected $hidden = [
        'remember_token',
        'password',
    ];

    public const USER_TYPE_SELECT = [
        'staff'       => 'staff',
        'beneficiary' => 'beneficiary',
    ];
    public const EMPLOYEE_TYPE_SELECT = [ 
        'specialist'  => 'أخصائي/ة',  
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'phone_2',
        'password',
        'approved',
        'email_verified_at',
        'remember_token',
        'identity_num',
        'user_type',
        'section_id',
        'employee_type',
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
    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }
    public function getIsBeneficiaryAttribute()
    {
        return $this->user_type == 'beneficiary';
    }
    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class, 'user_id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function userUserAlerts()
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    } 
    public function maritalStatuses()
    {
        return $this->belongsToMany(MaritalStatus::class, 'user_marital_status_pivot', 'user_id', 'marital_status_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')->withTimestamps()->withPivot('unread_count');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getLogNameToUse(): ?string
    {
        return $this->is_beneficiary ? 'user_beneficiary_activity-' . $this->id : 'default';
    }
    

    public function getLogAttributes()
    {
        return [
            'name',
            'email',
            'username',
            'phone',
            'phone_2',
            'password',
            'approved',  
            'identity_num', 
        ];
    }
    public function getActivityDescriptionForEvent($eventName)
    {
        if ($this->is_beneficiary) {
            if ($eventName == 'created') {
                return "تم إنشاء حساب دخول للمستفيد";
            } elseif ($eventName == 'updated') {
                return "تم تحديث حساب دخول المستفيد";
            } elseif ($eventName == 'deleted') {
                return "تم حذف حساب دخول المستفيد";
            }
        } else {
            return $eventName;
        }
    }

    public function getCustomAttributes(Activity $activity)
    {
        $properties = $activity->properties ?? [];

        $transformData = function ($data) use ($activity) {
            if (isset($data['password'])) {
                if ($activity->event == 'updated') {
                    $data['password'] = 'تم تحديث كلمة المرور';
                }else{
                    unset($data['password']);
                }
                if($activity->event == 'created'){
                    unset($data['approved']);
                }
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
