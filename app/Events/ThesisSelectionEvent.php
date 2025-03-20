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

    public $thesis;
    public $action;
    public $expiresAt;
    public $student_id;

    /**
     * Create a new event instance.
     */
    public function __construct(ThesisTitle $thesis, string $action, $expiresAt = null, $student_id = null)
    {
        $this->thesis = $thesis;
        $this->action = $action;
        $this->expiresAt = $expiresAt;
        $this->student_id = $student_id;
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
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id'         => $this->thesis->id,
            'action'     => $this->action,
            'expiresAt'  => $this->expiresAt,
            'student_id' => $this->student_id,
        ];
    }
}