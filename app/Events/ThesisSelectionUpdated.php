<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThesisSelectionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thesisId;
    public $status;
    public $expiresAt;
    public $action;

    public function __construct($thesisId, $status, $expiresAt, $action)
    {
        $this->thesisId = $thesisId;
        $this->status = $status;
        $this->expiresAt = $expiresAt;
        $this->action = $action; // 'select' or 'reset'
    }

    public function broadcastOn()
    {
        return new Channel('thesis-selection');
    }
}