<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Utils\LogsModelActivity;
use Carbon\Carbon;
use DateTimeInterface;

class ServiceLoanMember extends Model
{
    use HasFactory;
    use LogsModelActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_loan_members';

    public const STATUS_SELECT = [
        'pending' => 'قيد الانتظار',
        'approved' => 'تم الانضمام',
        'rejected' => 'تم الرفض',
    ];

    public const PROJECT_TYPE_SELECT = [
        'commercial'     => 'تجاري', 
        'industrial'    => 'صناعي',
        'service' => 'خدمي', 
    ];

    public const PROJECT_LOCATION_SELECT = [
        'inside'     => 'داخل المنزل', 
        'outside'    => 'خارج المنزل', 
    ];

    public const PROJECT_FINANCIAL_SOURCE_SELECT = [
        'social'     => 'ضامن اجتماعي', 
        'retirement'  => 'تقاعد',
        'other' => 'جهة تمويل أخرى', 
    ];

    public const MEMBER_POSITION_SELECT = [
        'responsible' => 'المسؤول',
        'member' => 'عضو',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_loan_id',
        'beneficiary_id', 
        'loan_id',
        'status',
        'name',
        'identity_number',
        'member_position',
        'project_type',
        'project_location',
        'district_id',
        'street',
        'project_start_date',
        'project_years_of_experience',
        'project_short_description',
        'project_financial_source',
        'purpose_of_loan',
        'has_previous_loan',
        'previous_loan_number',
        'installment',
        'months',
        'amount',
        'handle',
    ]; 

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    /**
     * Get the service loan that owns the member.
     */
    public function serviceLoan(): BelongsTo
    {
        return $this->belongsTo(ServiceLoan::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function getProjectStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setProjectStartDateAttribute($value)
    {
        $this->attributes['project_start_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    /**
     * Get the beneficiary that owns the member.
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * Scope a query to only include pending members.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved members.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected members.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
