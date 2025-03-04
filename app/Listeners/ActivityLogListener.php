<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ThesisSelected;
use App\Models\ThesisSelection;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivityLogListener implements ShouldQueue
{
    public function handle(ThesisSelected $event)
    {
        ActivityLog::create([
            'loggable_type' => ThesisSelection::class,
            'loggable_id'   => $event->selection->id,
            'action'        => 'Thesis selection created',
            'description'   => 'Student selected thesis title: ' . $event->selection->thesisTitle->title,
            'ip_address'    => request()->ip()
        ]);
    }
}
