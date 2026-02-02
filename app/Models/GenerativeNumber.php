<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class GenerativeNumber extends Model
{ 

    public $table = 'generative_numbers';

    protected $dates = [
        'created_at',
        'updated_at', 
    ];

    const MODEL_SELECT = [
        'IncomingLetter' => 'Incoming Letter',
        'OutgoingLetter' => 'Outgoing Letter', 
    ];
    const TYPE_SELECT = [
        'inner' => 'داخلي',
        'outer' => 'خارجي',
    ]; 

    protected $fillable = [
        'model',
        'type',
        'prefix',
        'number',
        'created_at',
        'updated_at', 
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    } 
}
