<?php

namespace App\Jobs;

use App\Services\ThesisTimerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessThesisTimerExpiration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thesisId;

    /**
     * Create a new job instance.
     *
     * @param int $thesisId
     * @return void
     */
    public function __construct($thesisId)
    {
        $this->thesisId = $thesisId;
    }

    /**
     * Execute the job.
     *
     * @param ThesisTimerService $timerService
     * @return void
     */
    public function handle(ThesisTimerService $timerService)
    {
        // Double-check that the timer is actually expired
        $timerStatus = $timerService->getTimerStatus($this->thesisId);
        
        // If timer still exists but has expired or is about to expire (within 1 second)
        if ($timerStatus && $timerStatus['time_left'] <= 1) {
            $timerService->handleExpiredTimer($this->thesisId);
        }
    }
}