<?php

namespace App\Models;

use App\Events\ConversationSeenEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = ['name', 'is_group'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')->withTimestamps()->withTrashed()->withPivot('unread_count');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    } 

    public function markAsSeen($userId)
    {
        // Mark messages as seen
        $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_seen', false)
            ->update([
                'is_seen' => true,
                'seen_at' => now()
            ]);

        // Reset unread count for this conversation for the user
        $this->users()->updateExistingPivot($userId, [
            'unread_count' => 0
        ]);
        

        event(new ConversationSeenEvent([
            'conversation_id' => $this->id,
            'user_id' => $userId,
            'seen_at' => now()
        ]));
    }
    // for one to one conversation
    public function otherParticipant($authUserId)
    {
        return $this->users->where('id', '!=', $authUserId)->first();
    }
    // Optional helper for 1-to-1 conversations
    public function isOneToOne()
    {
        return !$this->is_group && $this->users()->count() === 2;
    }
}
