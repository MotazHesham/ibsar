<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Utils\LogsModelActivity;
class EconomicStatus extends Model
{
    use SoftDeletes, HasFactory;
    use HasTranslations;
    use LogsModelActivity;
    public $table = 'economic_statuses';
    public array $translatable = ['name'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'income'  => 'دخل',
        'expense' => 'مصروف',
    ];

    public const DATA_TYPE_SELECT = [
        'number' => 'رقم/ مبلغ',
        'text'   => 'نص',
    ];

    protected $fillable = [
        'name',
        'type',
        'data_type',
        'is_required',
        'order_level',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
