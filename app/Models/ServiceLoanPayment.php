<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Utils\LogsModelActivity;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use DateTimeInterface;

class ServiceLoanPayment extends Model implements HasMedia
{
    use HasFactory;
    use LogsModelActivity;
    use InteractsWithMedia;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_loan_payments';

    protected $appends = [
        'payment_receipt',
    ];

    public const SELECT_PAYMENT_METHOD = [ 
        'bank_transfer' => 'تحويل بنكي',
        'check' => 'شيك',
        'other' => 'أخرى',
    ];
    public const SELECT_PAYMENT_STATUS = [
        'pending' => 'قيد الانتظار', 
        'approved_specialist' => 'موافقة أخصائية',
        'paid' => 'مدفوعة', 
        'rejected' => 'مرفوضة',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_loan_id',
        'payment_method',   
        'payment_status',
        'payment_reference_number',
        'paid_date',
        'amount',
        'note',
        'rejection_reason', 
        'handle',
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

    public function getPaymentReceiptAttribute()
    {
        $file = $this->getMedia('payment_receipt')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    /**
     * Get the service loan that owns the installment.
     */
    public function serviceLoan(): BelongsTo
    {
        return $this->belongsTo(ServiceLoan::class);
    }   

    public function getPaidDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setPaidDateAttribute($value)
    {
        $this->attributes['paid_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    } 
}
