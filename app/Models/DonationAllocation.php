<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\LogsModelActivity;

class DonationAllocation extends Model
{
    use HasFactory;
    use LogsModelActivity;

    public $table = 'donation_allocations';

    protected $fillable = [
        'donation_id',
        'beneficiary_order_id',
        'allocated_amount',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function beneficiaryOrder()
    {
        return $this->belongsTo(BeneficiaryOrder::class);
    }

    public function items()
    {
        return $this->hasMany(DonationAllocationItem::class, 'donation_allocation_id', 'id');
    }
}

