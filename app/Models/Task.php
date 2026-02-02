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

class Task extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    public $table = 'tasks';

    protected $appends = [
        'attachment',
    ];

    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'short_description',
        'description',
        'status_id',
        'task_priority_id',
        'due_date',
        'task_board_id',
        'assigned_by_id',
        'ordering',
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

    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    public function task_priority()
    {
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function tags()
    {
        return $this->belongsToMany(TaskTag::class);
    }

    public function getAttachmentAttribute()
    {
        return $this->getMedia('attachment');
    }

    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function assigned_tos()
    {
        return $this->belongsToMany(User::class);
    }

    public function task_board()
    {
        return $this->belongsTo(TaskBoard::class, 'task_board_id');
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }
    
    public function getActivityDescriptionForEvent($eventName){
        if ($eventName == 'created') {
            return 'تم أضافة مهمة جديدة';
        } elseif ($eventName == 'updated') {
            return "تم تحديث بيانات مهمة";
        } elseif ($eventName == 'deleted') {
            return 'تم حذف بيانات مهمة';
        }
    } 
    public function getLogNameToUse(): ?string
    {
        return 'task_activity-'.$this->id;
    }
    public function getLogAttributes()
    {
        return [
            'name',
            'short_description',
            'description',
            'due_date',
            'ordering',
            'status->id',
            'status->name',
            'task_priority->id',
            'task_priority->name',
            'task_board->id',
            'task_board->name',
            'assigned_by->id',
            'assigned_by->name',
            'tags->id',
            'tags->name',
            'assigned_tos->id',
            'assigned_tos->name',
        ];
    } 
}
