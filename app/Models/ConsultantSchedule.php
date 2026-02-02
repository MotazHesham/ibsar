<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class ConsultantSchedule extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'consultant_schedules';

    public const DAY_SELECT = [
        'Sunday'    => 'الأحد',
        'Monday'    => 'الاثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
        'Friday'    => 'الجمعة',
        'Saturday'  => 'السبت',
    ];

    public const ATTENDANCE_TYPE_SELECT = [
        'online'     => 'أونلاين',
        'in_person'  => 'حضوري',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'consultant_id',
        'day',
        'start_time',
        'end_time',
        'slot_duration',
        'attendance_type',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'consultant_id');
    }
} 