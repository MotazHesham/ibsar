<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations; 
use App\Utils\LogsModelActivity;

class BeneficiaryFile extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    use HasTranslations;

    protected $appends = [
        'file',
    ];

    public $table = 'beneficiary_files';

    public array $translatable = ['name'];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'beneficiary_id',
        'name',
        'required_document_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function getFileAttribute()
    {
        return $this->getMedia('file')->last();
    }

    public function required_document()
    {
        return $this->belongsTo(RequiredDocument::class, 'required_document_id');
    } 
}
