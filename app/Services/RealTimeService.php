<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ThesisTitle;
use App\Models\LiveActivity;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class RealTimeService
{
    /**
     * Broadcast thesis selection to all connected clients
     * 
     * @param Student $student
     * @param ThesisTitle $thesisTitle
     * @return void
     */
    public function broadcastSelection(Student $student, ThesisTitle $thesisTitle)
    {
        try {
            event(new \App\Events\ThesisSelected([
                'student_name'  => $student->name,
                'student_class' => $student->class,
                'thesis_title'  => $thesisTitle->title,
                'thesis_topic'  => $thesisTitle->topic,
                'timestamp'     => now()->toDateTimeString(),
                'status'        => 'selected'
            ]));

            $this->logActivity($student->id, $thesisTitle->id, 'broadcast_selection');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to broadcast thesis selection: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast thesis title status update
     * 
     * @param ThesisTitle $thesisTitle
     * @param string $status
     * @return void
     */
    public function broadcastStatusUpdate(ThesisTitle $thesisTitle, string $status)
    {
        try {
            event(new \App\Events\ThesisTitleUpdated([
                'thesis_id'    => $thesisTitle->id,
                'thesis_title' => $thesisTitle->title,
                'thesis_topic' => $thesisTitle->topic,
                'status'       => $status,
                'timestamp'    => now()->toDateTimeString(),
            ]));

            $this->logActivity(null, $thesisTitle->id, 'status_update_' . $status);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to broadcast status update: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Broadcast student activity
     * 
     * @param Student|null $student
     * @param string $action
     * @param array $data
     * @return bool
     */
    public function broadcastActivity(?Student $student, string $action, array $data = [])
    {
        try {
            $payload = [
                'action'    => $action,
                'timestamp' => now()->toDateTimeString(),
                'data'      => $data
            ];

            if ($student) {
                $payload['student_id'] = $student->id;
                $payload['student_name'] = $student->name;
                $payload['student_class'] = $student->class;
            }

            event(new \App\Events\LiveActivityLogged($payload));

            $studentId = $student ? $student->id : null;
            $this->logActivity($studentId, null, $action);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to broadcast activity: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log real-time activity
     * 
     * @param int|null $studentId
     * @param int|null $thesisTitleId
     * @param string $action
     * @return void
     */
    protected function logActivity(?int $studentId, ?int $thesisTitleId, string $action)
    {
        LiveActivity::create([
            'student_id'      => $studentId,
            'thesis_title_id' => $thesisTitleId,
            'action'          => $action,
            'ip_address'      => request()->ip()
        ]);
    }
}