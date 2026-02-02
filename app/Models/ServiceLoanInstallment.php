<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Utils\LogsModelActivity;
use Carbon\Carbon;
use DateTimeInterface;

class ServiceLoanInstallment extends Model
{
    use HasFactory;
    use LogsModelActivity;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_loan_installments';  
    
    public const SELECT_PAYMENT_STATUS = [
        'pending' => 'Pending',
        'paid' => 'Paid', 
    ];

    protected $fillable = [
        'service_loan_id',
        'installment',
        'installment_date',
        'paid_amount', 
        'payment_status', 
    ];


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    } 

    /**
     * Get the service loan that owns the installment.
     */
    public function serviceLoan(): BelongsTo
    {
        return $this->belongsTo(ServiceLoan::class);
    }

    /**
     * Get the installment amount from service loan
     */
    public function getInstallmentAmountAttribute(): float
    {
        return $this->serviceLoan->installment ?? 0;
    }

    /**
     * Get the remaining amount for this installment
     */
    public function getRemainingAmountAttribute(): float
    {
        $installmentAmount = $this->installment_amount;
        $paidAmount = $this->paid_amount ?? 0;
        return max(0, $installmentAmount - $paidAmount);
    }

    /**
     * Scope a query to only include paid installments.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope a query to only include overdue installments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('installment_date', '<', now()->toDateString())
                    ->where('payment_status', '!=', 'paid');
    }

    /**
     * Scope a query to only include upcoming installments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('installment_date', '>=', now()->toDateString())
                    ->where('payment_status', '!=', 'paid');
    }

    /**
     * Check if the installment is overdue.
     */
    public function isOverdue(): bool
    {
        $installmentDate = $this->installment_date ? Carbon::createFromFormat(config('panel.date_format'), $this->installment_date)->format('Y-m-d') : null;
        return $installmentDate < now()->toDateString() && $this->payment_status !== 'paid';
    }

    /**
     * Check if the installment is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the installment is pending.
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }
    
    public function getInstallmentDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setInstallmentDateAttribute($value)
    {
        $this->attributes['installment_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
