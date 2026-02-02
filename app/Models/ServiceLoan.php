<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;

class ServiceLoan extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;

    public $table = 'service_loans';

    protected $dates = [
        'project_start_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'pending' => 'قيد المراجعة',
        'loan_paid' => 'تم الصرف', 
    ];

    protected $fillable = [
        'beneficiary_order_id',
        'status',
        'group_name',
        'amount',
        'installment',
        'months',
        'loan_id',
        'kafil_name',
        'kafil_identity_num',
        'accommodation_type_id',
        'marital_status_id',
        'educational_qualification_id',
        'job_type_id',
        'kafil_district_id',
        'kafil_street',
        'kafil_nearby_address',
        'kafil_phone',
        'kafil_phone2',
        'kafil_work_phone',
        'kafil_work_address',
        'kafil_email',
        'kafil_work_name',
        'kafil_mail_box',
        'kafil_postal_code',
        'contacts',
        'created_at',
        'updated_at',
        'deleted_at',
    ]; 

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function beneficiary_order()
    {
        return $this->belongsTo(BeneficiaryOrder::class, 'beneficiary_order_id');
    }

    public function members()
    {
        return $this->hasMany(ServiceLoanMember::class, 'service_loan_id');
    }

    public function installments()
    {
        return $this->hasMany(ServiceLoanInstallment::class, 'service_loan_id');
    }

    public function payments()
    {
        return $this->hasMany(ServiceLoanPayment::class, 'service_loan_id');
    }

    public function accommodationType()
    {
        return $this->belongsTo(AccommodationType::class, 'accommodation_type_id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }

    public function educationalQualification()
    {
        return $this->belongsTo(EducationalQualification::class, 'educational_qualification_id');
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class, 'job_type_id');
    }

    public function kafilDistrict()
    {
        return $this->belongsTo(District::class, 'kafil_district_id');
    }

    public function addInstallments($startDate)
    { 
        $installments = [];
        for($i = 0; $i < $this->months; $i++){
            $installments[] = [ 
                'service_loan_id' => $this->id,
                'installment_date' => Carbon::parse($startDate)->addMonths($i + 1),
                'installment' => $this->installment,
                'payment_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        ServiceLoanInstallment::insert($installments); 
    }

    /**
     * Get total amount paid
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments->where('payment_status', 'paid')->sum('amount');
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->total_paid;
    }

    /**
     * Check if loan is fully paid
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0;
    }
} 