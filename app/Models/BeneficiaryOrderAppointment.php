<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use DateTimeInterface;
use App\Utils\LogsModelActivity;

class BeneficiaryOrderAppointment extends Model
{
    use HasFactory;
    use LogsModelActivity;

    public $table = 'beneficiary_order_appointments';

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
    public const STATUS_SELECT = [
        'pending' => 'قيد الإنتظار',
        'confirmed' => 'تم التأكيد',
        'canceled' => 'تم الإلغاء',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'beneficiary_order_id',
        'consultation_type_id',
        'beneficiary_id',
        'consultant_id',
        'day',
        'date',
        'time',
        'duration',
        'attendance_type',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function beneficiaryOrder()
    {
        return $this->belongsTo(BeneficiaryOrder::class, 'beneficiary_order_id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'consultant_id');
    }
    
    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
    
    public function getActivityDescriptionForEvent($eventName){
        if ($eventName == 'created') {
            return 'تم أضافة حجز جديد رقم ' . $this->id;
        } elseif ($eventName == 'updated') {
            return 'تم تحديث بيانات الحجز رقم ' . $this->id;
        } elseif ($eventName == 'deleted') {
            return 'تم حذف بيانات الحجز رقم ' . $this->id;
        }
    } 

    public function getLogNameToUse(): ?string
    {
        return 'beneficiary_order_activity-'.$this->beneficiary_order_id;
    }

    public function getLogAttributes()
    {
        return [  
            'day',
            'date',
            'time',
            'duration',
            'attendance_type',
            'status', 

            'consultation_type->id',
            'consultation_type->name',
            'beneficiary->id',
            'beneficiary->name',
            'consultant->id',
            'consultant->name',
        ];
    }
}
