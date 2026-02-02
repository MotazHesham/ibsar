<?php

namespace App\Models;

use App\Observers\OutgoingLetterObserver;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Utils\LogsModelActivity;
class OutgoingLetter extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    use LogsModelActivity;
    public $table = 'outgoing_letters';

    protected $appends = [
        'attachments',
    ];

    public const OUTGOING_TYPE_SELECT = [
        'inner' => 'صادر داخلي',
        'outer' => 'صادر خارجي',
    ];

    protected $dates = [
        'letter_date',
        'delivered_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PRIORITY_SELECT = [
        'normal'    => 'عادي',
        'important' => 'مهم',
        'urgent'    => 'عاجل',
    ];

    protected $fillable = [
        'letter_number',
        'letter_date',
        'delivered_date',
        'recevier_id',
        'subject',
        'letter',
        'priority',
        'outgoing_type',
        'note',
        'incoming_letter_id',
        'is_archived',
        'created_by_id',
        'letter_organization_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();
        OutgoingLetter::observe(OutgoingLetterObserver::class);
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

    public function getDeliveredDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDeliveredDateAttribute($value)
    {
        $this->attributes['delivered_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function recevier()
    {
        return $this->belongsTo(User::class, 'recevier_id');
    }

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    }

    public function incoming_letter()
    {
        return $this->belongsTo(IncomingLetter::class, 'incoming_letter_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function letter_organization()
    {
        return $this->belongsTo(LettersOrganization::class, 'letter_organization_id');
    }
}
