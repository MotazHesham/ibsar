<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ConversationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $user_id;
    public $user_name;
    public $conversation_id;
    public $message;
    public $files;
    public $time;
    public $message_html;

    public function __construct($data)
    {
        $this->user_id = $data['user_id'];
        $this->user_name = $data['user_name'];
        $this->conversation_id = $data['conversation_id'];
        $this->message = $data['message'];
        $this->files = $data['files'];
        $this->time = $data['time'];
        $this->message_html = $data['message_html'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return ['conversation.' . $this->conversation_id];
    }
}
