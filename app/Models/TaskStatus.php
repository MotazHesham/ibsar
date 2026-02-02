<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
class TaskStatus extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;
    public $table = 'task_statuses';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const BADGE_CLASS_SELECT = [
        'primary' => 'Primary',
        'success' => 'Success',
        'warning' => 'Warning',
        'danger' => 'Danger',
        'info' => 'Info',
        'dark' => 'Dark',
        'light' => 'Light',
        'secondary' => 'Secondary',
    ];

    protected $fillable = [
        'name',
        'badge_class',
        'ordering',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
