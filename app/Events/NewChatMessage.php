<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    /**
     * Create a new event instance.
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.student.' . $this->chat->student_id),
            new PrivateChannel('chat.admin'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id'                  => $this->chat->id,
            'student_id'          => $this->chat->student_id,
            'user_id'             => $this->chat->user_id,
            'message'             => $this->chat->message,
            'sender_type'         => $this->chat->sender_type,
            'thesis_selection_id' => $this->chat->thesis_selection_id,
            'created_at'          => $this->chat->created_at->format('Y-m-d H:i:s'),
            'sender_name'         => $this->chat->sender_type === 'student'
                ? $this->chat->student->name
                : $this->chat->user->name,
        ];
    }
}