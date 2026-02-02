<?php

namespace App\Models;

use App\Observers\IncomingLetterObserver;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Utils\LogsModelActivity;
class IncomingLetter extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    public $table = 'incoming_letters';

    protected $appends = [
        'attachments',
    ];

    protected $dates = [
        'letter_date',
        'received_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const INCOMING_TYPE_SELECT = [
        'inner' => 'وارد داخلي',
        'outer' => 'وارد خارجي',
    ];

    public const PRIORITY_SELECT = [
        'normal'    => 'عادي',
        'important' => 'مهم',
        'urgent'    => 'عاجل',
    ];

    protected $fillable = [
        'letter_number',
        'external_number',
        'letter_date',
        'received_date',
        'recevier_id',
        'subject',
        'letter',
        'priority',
        'incoming_type',
        'letter_organization_id',
        'note',
        'outgoing_letter_id',
        'is_archived',
        'created_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        IncomingLetter::observe(IncomingLetterObserver::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getLetterDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setLetterDateAttribute($value)
    {
        $this->attributes['letter_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getReceivedDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setReceivedDateAttribute($value)
    {
        $this->attributes['received_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function recevier()
    {
        return $this->belongsTo(User::class, 'recevier_id');
    }

    public function letter_organization()
    {
        return $this->belongsTo(LettersOrganization::class, 'letter_organization_id');
    }

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    }

    public function outgoing_letter()
    {
        return $this->belongsTo(OutgoingLetter::class, 'outgoing_letter_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    
    public function archives(): MorphMany
    {
        return $this->morphMany(Archive::class, 'archiveable');
    }
}
