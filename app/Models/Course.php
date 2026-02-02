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
class Course extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    public $table = 'courses';

    protected $appends = [
        'photo',
    ];

    public const ATTEND_TYPE_SELECT = [
        'online' => 'أونلاين',
        'attend' => 'حضوري',
    ];

    protected $dates = [
        'start_at',
        'end_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const CERTIFICATE_SELECT = [
        'none'  => 'لايوجد',
        'money' => 'مقابل مبلغ',
        'free'  => 'مجانية',
    ];

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'attend_type',
        'certificate',
        'trainer',
        'start_at',
        'end_at',
        'published',
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

    public function courseCourseStudents()
    {
        return $this->hasMany(CourseStudent::class, 'course_id', 'id');
    }

    public function getStartAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setEndAtAttribute($value)
    {
        $this->attributes['end_at'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function scopeActive($query)
    {
        return $query->where('published', 1)
            ->where('start_at', '<=', date('Y-m-d'))
            ->where('end_at', '>=', date('Y-m-d'));
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

    public function attendanceRoute()
    {
        return route('frontend.course-attendance', encrypt($this->id)); 
    }

    public function certificateRoute()
    {
        return route('frontend.course-certificate', encrypt($this->id)); 
    }
}
