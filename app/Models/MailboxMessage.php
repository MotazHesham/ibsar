<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MailboxMessage extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;
    public $table = 'mailbox_messages';

    protected $appends = [
        'attachments',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'subject',
        'content',
        'sender_id',
        'parent_id',
        'thread_id',
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

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    }

    public function participants()
    {
        return $this->hasMany(MessageParticipant::class, 'message_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->participants->firstWhere('role', 'receiver')?->user;
    }

    public function parent()
    {
        return $this->belongsTo(MailboxMessage::class, 'parent_id');
    }

    public function thread()
    {
        return $this->belongsTo(MailboxMessage::class, 'thread_id');
    }
    public function scopeForUser($query, $userId, $type)
    {
        return $query->whereHas('participants', function ($q) use ($userId, $type) {
            $q->where('user_id', $userId);
            switch($type) {
                case 'inbox':
                    $q->where('role', 'receiver')->where('is_deleted', false)->where('is_archived', false);
                    break;
                case 'sent':
                    $q->where('role', 'sender')->where('is_deleted', false)->where('is_archived', false);
                    break;
                case 'important':
                    $q->where('is_important', true)->where('is_deleted', false)->where('is_archived', false);
                    break;
                case 'starred':
                    $q->where('is_starred', true)->where('is_deleted', false)->where('is_archived', false);
                    break;
                case 'trash':
                    $q->where('is_deleted', true);
                    break;
                case 'archive':
                    $q->where('is_archived', true)->where('is_deleted', false);
                    break;
            } 
        });
    }
}
