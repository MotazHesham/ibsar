<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
class StorageLocation extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;
    public $table = 'storage_locations';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type',
        'code',
        'name',
        'description',
        'parent_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'shelf'     => 'رف',
        'cabinet'   => 'خزانة',
        'box'       => 'صندوق',
        'room'      => 'غرفة',
        'warehouse' => 'مستودع',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function parentStorageLocations()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function storageLocationArchives()
    {
        return $this->hasMany(Archive::class, 'storage_location_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
