<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteerTask extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'volunteer_tasks';

    public const VISIT_TYPE_SELECT = [
        'قسم الاسكان'  => 'قسم الاسكان',
        'لجنة التسكين' => 'لجنة التسكين',
        'أخرى'         => 'أخرى',
    ];

    public const STATUS_SELECT = [
        'pending'   => 'قيد الانتظار',
        'approved'  => 'موافق عليه',
        'rejected'  => 'مرفوض',
        'cancelled' => 'ملغي',
        'completed' => 'مكتمل',
    ];

    protected $fillable = [
        'name',
        'identity',
        'address',
        'phone',
        'details',
        'visit_type',
        'date',
        'arrive_time',
        'leave_time',
        'status',
        'cancel_reason',
        'notes',
        'volunteer_id',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id');
    }

    
    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
