<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
class Service extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;
    public $table = 'services';

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',       
    ];

    protected $fillable = [
        'type',
        'key_name',
        'title',
        'quantity',
        'start_date',
        'end_date',
        'active',
        'created_at',
        'updated_at',
        'deleted_at',
    ]; 

    public const TYPE_SELECT = [
        'financial'  => 'مالي',
        'housing'    => 'إسكان',
        'social'     => 'إجتماعي', 
        'consultant' => 'إستشاري',
        'courses'    => 'دورات',
        'loan'       => 'قروض',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function serviceBeneficiaryOrders()
    {
        return $this->hasMany(BeneficiaryOrder::class, 'service_id', 'id');
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
