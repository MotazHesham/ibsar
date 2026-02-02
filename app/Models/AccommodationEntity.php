<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Utils\LogsModelActivity;

class AccommodationEntity extends Model
{
    use SoftDeletes, HasFactory;
    use HasTranslations;
    use LogsModelActivity;

    public $table = 'accommodation_entities';
    public array $translatable = ['name'];

    public static $TYPE_SELECT = [
        'charity' => 'خيري',
        'social'  => 'تنموي',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
} 