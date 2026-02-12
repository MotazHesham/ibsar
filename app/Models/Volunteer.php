<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Volunteer extends Model implements AuthenticatableContract, HasMedia
{
    use Authenticatable, HasFactory, SoftDeletes, InteractsWithMedia;

    public $table = 'volunteers';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name',
        'identity_num',
        'email',
        'password',
        'phone_number',
        'interest',
        'initiative_name',
        'prev_experience',
        'approved',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['password'] = null;
            return;
        }
        // Avoid double hashing (e.g. when loading from DB and saving again)
        if (strlen($value) === 60 && preg_match('/^\$2[ay]\$\d{2}\$/', $value)) {
            $this->attributes['password'] = $value;
            return;
        }
        $this->attributes['password'] = Hash::make($value);
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function volunteerTasks()
    {
        return $this->hasMany(VolunteerTask::class, 'volunteer_id', 'id');
    }

    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
            $file->preview_url = $file->getUrl('preview');
            $file->file_name = $file->file_name;
        }
        return $file;
    }

    public function getCvAttribute()
    {
        $file = $this->getMedia('cv')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->file_name = $file->file_name;
        }
        return $file;
    }
}
