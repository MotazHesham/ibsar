<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\LogsModelActivity;

class DonationItem extends Model
{
    use HasFactory;
    use LogsModelActivity;

    public $table = 'donation_items';

    protected $fillable = [
        'donation_id',
        'item_name',
        'quantity',
        'unit_price',
        'total_price',
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
}

