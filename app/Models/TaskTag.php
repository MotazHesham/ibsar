<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
class TaskTag extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;
    public $table = 'task_tags';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'badge_class',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const BADGE_CLASS_SELECT = [
        'badge bg-primary-transparent' => 'Primary',
        'badge bg-secondary-transparent' => 'Secondary',
        'badge bg-success-transparent' => 'Success',
        'badge bg-danger-transparent' => 'Danger',
        'badge bg-warning-transparent' => 'Warning',
        'badge bg-info-transparent' => 'Info',
        'badge bg-dark-transparent' => 'Dark',
        'badge bg-light-transparent text-dark' => 'Light',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
