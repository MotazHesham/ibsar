<?php

namespace App\Services;

use App\Events\ConversationEvent;
use App\Models\Conversation;
use App\Models\Message;

class ChatService
{
    public function sendMessage($sender,$conversation, $message, $attachments = [],)
    {
        $message = Message::create([
            'user_id' => $sender->id,
            'message' => $message,
            'conversation_id' => $conversation->id
        ]);

        foreach ($attachments as $file) {
            $message->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('attachments');
        }

        // increase unread count for other participant
        $conversationUsers = $conversation->users() 
            ->get();
        foreach ($conversationUsers as $user) {
            if ($user->id == $sender->id) {
                continue;
            }
            $user->conversations()
                ->updateExistingPivot($conversation->id, [
                    'unread_count' => $user->pivot->unread_count + 1
                ]);
        }

        $otherParticipant = $conversation->otherParticipant($sender->id); 

        // event to notify the other participant
        $eventData = [
            'user_id' => $sender->id,
            'user_name' => $sender->name,
            'conversation_id' => $conversation->id,
            'message' => $message->message,
            'message_html' => view(
                'admin.chats.message',
                ['message' => $message, 'otherParticipant' => $sender, 'authUser' => $otherParticipant]
            )->render(),
            'files' => $message->attachments->toArray(),
            'time' => $message->getTime()
        ];
        event(new ConversationEvent($eventData));

        return view(
            'admin.chats.message',
            ['message' => $message, 'otherParticipant' => $otherParticipant, 'authUser' =>  $sender]
        )->render();
    }
    public function findOneToOneConversation($user1_id, $user2_id)
    {
        $conversation = Conversation::where('is_group', false)
            ->whereHas('users', fn($q) => $q->where('user_id', $user1_id))
            ->whereHas('users', fn($q) => $q->where('user_id', $user2_id))
            ->withCount('users')
            ->having('users_count', 2)
            ->first();
        if (!$conversation) {
            $conversation = Conversation::create([
                'is_group' => false,
            ]);
            $conversation->users()->attach([$user1_id, $user2_id]);
        }
        return  request()->user()
            ->conversations()
            ->find($conversation->id);
    }
}
