<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThesisSelected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $selection;

    public function __construct($selection)
    {
        $this->selection = $selection;
    }

    public function broadcastOn()
    {
        return ['thesis-selection'];
    }
}
