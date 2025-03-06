<?php

namespace App\Events;

use App\Models\ThesisTitle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThesisSelectionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thesisTitle;
    public $action;
    public $timestamp;
    public $expiresAt;

    /**
     * Create a new event instance.
     */
    public function __construct(ThesisTitle $thesisTitle, $action = 'selected', $expiresAt = null)
    {
        $this->thesisTitle = $thesisTitle;
        $this->action = $action; // 'selected', 'expired', 'available'
        $this->timestamp = now()->timestamp;
        $this->expiresAt = $expiresAt ? $expiresAt->timestamp : now()->addMinute()->timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('thesis-selections'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'thesis.update';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id'        => $this->thesisTitle->id,
            'title'     => $this->thesisTitle->title,
            'status'    => $this->thesisTitle->status,
            'action'    => $this->action,
            'timestamp' => $this->timestamp,
            'expiresAt' => $this->expiresAt,
        ];
    }
}