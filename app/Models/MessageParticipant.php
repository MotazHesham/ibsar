<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class MessageParticipant extends Model
{
    use HasFactory;
    public $table = 'mailbox_message_participants';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    const ROLES = [
        'sender' => 'sender',
        'receiver' => 'receiver',
    ];

    protected $fillable = [
        'message_id',
        'user_id',
        'role',
        'is_read',
        'is_starred',
        'is_important',
        'is_archived',
        'is_deleted',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function message()
    {
        return $this->belongsTo(MailboxMessage::class, 'message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
