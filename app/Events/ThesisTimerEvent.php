<?php

namespace App\Events;

use App\Models\ThesisTitle;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThesisTimerEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thesisId;
    public $action;
    public $data;
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @param int $thesisId
     * @param string $action
     * @param array $data
     * @return void
     */
    public function __construct($thesisId, $action, $data = [])
    {
        $this->thesisId = $thesisId;
        $this->action = $action;
        $this->data = $data;
        $this->timestamp = now()->timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('thesis-timers');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'timer.update';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id'        => $this->thesisId,
            'action'    => $this->action,
            'data'      => $this->data,
            'timestamp' => $this->timestamp
        ];
    }
}