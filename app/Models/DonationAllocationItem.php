<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\LogsModelActivity;

class DonationAllocationItem extends Model
{
    use HasFactory;
    use LogsModelActivity;

    public $table = 'donation_allocation_items';

    protected $fillable = [
        'donation_allocation_id',
        'donation_item_id',
        'allocated_quantity',
        'allocated_amount',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'allocated_quantity' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function donationAllocation()
    {
        return $this->belongsTo(DonationAllocation::class);
    }

    public function donationItem()
    {
        return $this->belongsTo(DonationItem::class);
    }
}
