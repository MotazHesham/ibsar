<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Archive extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'archives';

    public const ARCHIVEABLE_TYPES = [
        'App\Models\Beneficiary' => 'مستفيد',
        'App\Models\IncomingLetter' => 'مراسلة واردة',
        'App\Models\OutgoingLetter' => 'مراسلة صادرة',
        'App\Models\BeneficiaryOrder' => 'طلب مستفيد', 
    ];

    protected $dates = [
        'archived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'archived_at',
        'archived_by_id',
        'archive_reason',
        'metadata',
        'storage_location_id',
        'archiveable_id',
        'archiveable_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getArchivedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setArchivedAtAttribute($value)
    {
        $this->attributes['archived_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function archived_by()
    {
        return $this->belongsTo(User::class, 'archived_by_id');
    }

    public function storage_location()
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id');
    }
    
    public function archiveable(): MorphTo
    {
        return $this->morphTo();
    } 
}
