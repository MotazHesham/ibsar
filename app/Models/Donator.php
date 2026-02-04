<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\LogsModelActivity;

class Donator extends Model
{
    use HasFactory;
    use LogsModelActivity;

    public $table = 'donators';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donator_id', 'id');
    }
}

