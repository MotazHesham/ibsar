<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Utils\LogsModelActivity;
class FamilyRelationship extends Model
{
    use SoftDeletes, HasFactory;
    use HasTranslations;
    use LogsModelActivity;
    public $table = 'family_relationships';
    public array $translatable = ['name'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const GENDER_RADIO = [
        'male'   => 'ذكر',
        'female' => 'انثي',
    ];

    protected $fillable = [
        'gender',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
