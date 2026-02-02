<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FrontLink extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'front_links';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const POSITION_SELECT = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
    ];

    protected $fillable = [
        'name',
        'link',
        'position',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
