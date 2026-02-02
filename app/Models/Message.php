<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Message extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    protected $table = 'messages';
    protected $fillable = ['conversation_id', 'user_id', 'message', 'is_seen', 'seen_at'];

    
    protected $appends = [
        'attachments',
    ];
    public function getTime(){
        return $this->created_at->format('h:i a') ?? null;
    } 
    
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    } 

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
