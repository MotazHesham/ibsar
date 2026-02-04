<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\LogsModelActivity;
use Carbon\Carbon;

class Donation extends Model
{
    use HasFactory;
    use LogsModelActivity;

    public const TYPE_MONEY = 'money';
    public const TYPE_ITEMS = 'items';

    public const DONATION_TYPE_SELECT = [
        self::TYPE_MONEY => 'Money',
        self::TYPE_ITEMS => 'Items',
    ];

    public $table = 'donations';

    protected $dates = [
        'donated_at',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'donator_id',
        'project_id',
        'donation_type',
        'total_amount',
        'notes',
        'donated_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'total_amount'     => 'decimal:2',
        'used_amount'      => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function getDonatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }
    public function setDonatedAtAttribute($value)
    {
        $this->attributes['donated_at'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function donator()
    {
        return $this->belongsTo(Donator::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(DonationItem::class, 'donation_id', 'id');
    }

    public function allocations()
    {
        return $this->hasMany(DonationAllocation::class, 'donation_id', 'id');
    }

    public function beneficiaryOrders()
    {
        return $this->belongsToMany(BeneficiaryOrder::class, 'donation_allocations', 'donation_id', 'beneficiary_order_id');
    }
}

