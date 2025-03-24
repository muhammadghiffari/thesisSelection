<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\ThesisTitle;
use App\Models\ThesisSelection;
use App\Events\ThesisTimerEvent;
use App\Events\ThesisSelectionEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThesisTimerService
{
    const TIMER_KEY_PREFIX = 'thesis_timer:';
    const LOCK_KEY_PREFIX = 'thesis_lock:';
    const TIMER_DURATION = 60; // in seconds
    
    /**
     * Start a new timer for thesis selection
     *
     * @param int $thesisId
     * @param int $studentId
     * @return array|null
     */
    public function startTimer($thesisId, $studentId)
    {
        // Acquire a distributed lock to prevent race conditions
        $lockKey = self::LOCK_KEY_PREFIX . $thesisId;
        $lockId = Str::uuid()->toString();
        
        // Try to get a lock with 5 second timeout
        if (!Redis::set($lockKey, $lockId, 'EX', 5, 'NX')) {
            return null; // Could not acquire lock, another process is updating this thesis
        }
        
        try {
            // Check if thesis is available in database
            $thesis = ThesisTitle::find($thesisId);
            if (!$thesis || $thesis->status !== 'Available') {
                return null;
            }
            
            // Start a database transaction
            DB::beginTransaction();
            
            // Update thesis status
            $thesis->status = 'In Selection';
            $thesis->save();
            
            // Create expiration timestamp
            $expiresAt = now()->addSeconds(self::TIMER_DURATION);
            
            // Store in database
            $selection = new ThesisSelection([
                'student_id' => $studentId,
                'thesis_title_id' => $thesisId,
                'ip_address' => request()->ip(),
                'status' => 'Pending',
                'expires_at' => $expiresAt
            ]);
            $selection->save();
            
            // Store in Redis for faster lookup and synchronized timers
            $timerKey = self::TIMER_KEY_PREFIX . $thesisId;
            $timerData = [
                'thesis_id' => $thesisId,
                'student_id' => $studentId,
                'selection_id' => $selection->id,
                'expires_at' => $expiresAt->timestamp
            ];
            
            Redis::hmset($timerKey, $timerData);
            Redis::expire($timerKey, self::TIMER_DURATION + 5); // Add 5 seconds buffer
            
            // Commit database changes
            DB::commit();
            
            // Broadcast the timer start event
            event(new ThesisSelectionEvent($thesis, 'selected', $expiresAt));
            
            // Return timer data
            return [
                'thesis_id' => $thesisId,
                'selection_id' => $selection->id,
                'expires_at' => $expiresAt->timestamp
            ];
        } catch (\Exception $e) {
            // Rollback on error
            DB::rollBack();
            return null;
        } finally {
            // Release the lock if we're still holding it
            $this->releaseLock($lockKey, $lockId);
        }
    }
    
    /**
     * Get current timer status for a thesis
     *
     * @param int $thesisId
     * @return array|null
     */
    public function getTimerStatus($thesisId)
    {
        $timerKey = self::TIMER_KEY_PREFIX . $thesisId;
        
        // Check Redis first for performance
        if (Redis::exists($timerKey)) {
            $timerData = Redis::hgetall($timerKey);
            
            // Verify the timer hasn't expired
            if (time() < (int)$timerData['expires_at']) {
                return [
                    'thesis_id' => $timerData['thesis_id'],
                    'student_id' => $timerData['student_id'],
                    'selection_id' => $timerData['selection_id'],
                    'expires_at' => (int)$timerData['expires_at'],
                    'time_left' => (int)$timerData['expires_at'] - time()
                ];
            }
            
            // Redis timer exists but has expired - clean it up
            $this->handleExpiredTimer($thesisId);
            return null;
        }
        
        // Fallback to database if not in Redis
        $activeSelection = ThesisSelection::where('thesis_title_id', $thesisId)
            ->where('status', 'Pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
            
        if ($activeSelection) {
            // Recreate in Redis for consistency
            $timerKey = self::TIMER_KEY_PREFIX . $thesisId;
            $timerData = [
                'thesis_id' => $thesisId,
                'student_id' => $activeSelection->student_id,
                'selection_id' => $activeSelection->id,
                'expires_at' => $activeSelection->expires_at->timestamp
            ];
            
            $timeLeft = $activeSelection->expires_at->timestamp - time();
            if ($timeLeft > 0) {
                Redis::hmset($timerKey, $timerData);
                Redis::expire($timerKey, $timeLeft + 5); // Add 5 seconds buffer
                
                return array_merge($timerData, ['time_left' => $timeLeft]);
            }
            
            // Timer has expired in database
            $this->handleExpiredTimer($thesisId);
        }
        
        return null;
    }
    
    /**
     * Handle expiration of a timer
     *
     * @param int $thesisId
     * @return bool
     */
    public function handleExpiredTimer($thesisId)
    {
        // Acquire a distributed lock to prevent race conditions
        $lockKey = self::LOCK_KEY_PREFIX . $thesisId;
        $lockId = Str::uuid()->toString();
        
        // Try to get a lock with 5 second timeout
        if (!Redis::set($lockKey, $lockId, 'EX', 5, 'NX')) {
            return false; // Could not acquire lock
        }
        
        try {
            DB::beginTransaction();
            
            // Update thesis status in database
            $thesis = ThesisTitle::find($thesisId);
            if ($thesis && $thesis->status == 'In Selection') {
                $thesis->status = 'Available';
                $thesis->save();
                
                // Mark pending selections as expired
                ThesisSelection::where('thesis_title_id', $thesisId)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Expired']);
                
                // Clean up Redis
                $timerKey = self::TIMER_KEY_PREFIX . $thesisId;
                Redis::del($timerKey);
                
                // Broadcast the expiration event
                event(new ThesisSelectionEvent($thesis, 'expired'));
                
                DB::commit();
                return true;
            }
            
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } finally {
            // Release the lock
            $this->releaseLock($lockKey, $lockId);
        }
    }
    
    /**
     * Complete a thesis selection successfully
     *
     * @param int $thesisId
     * @param int $studentId
     * @return bool
     */
    public function completeSelection($thesisId, $studentId)
    {
        // Acquire a distributed lock
        $lockKey = self::LOCK_KEY_PREFIX . $thesisId;
        $lockId = Str::uuid()->toString();
        
        if (!Redis::set($lockKey, $lockId, 'EX', 5, 'NX')) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            // Verify timer is still active and belongs to this student
            $timerStatus = $this->getTimerStatus($thesisId);
            if (!$timerStatus || $timerStatus['student_id'] != $studentId) {
                DB::rollBack();
                return false;
            }
            
            // Update thesis title status
            $thesis = ThesisTitle::find($thesisId);
            if (!$thesis) {
                DB::rollBack();
                return false;
            }
            
            $thesis->status = 'Unavailable';
            $thesis->save();
            
            // Update selection status
            $selection = ThesisSelection::find($timerStatus['selection_id']);
            if ($selection) {
                $selection->status = 'Approved';
                $selection->save();
            }
            
            // Update student status
            $student = \App\Models\Student::find($studentId);
            if ($student) {
                $student->has_selected = true;
                $student->save();
                
                // Log activity
                $student->activityLogs()->create([
                    'action' => 'thesis_selection',
                    'description' => 'Mahasiswa memilih judul skripsi: ' . $thesis->title,
                    'ip_address' => request()->ip()
                ]);
            }
            
            // Clean up Redis
            $timerKey = self::TIMER_KEY_PREFIX . $thesisId;
            Redis::del($timerKey);
            
            // Broadcast selection complete event
            event(new ThesisSelectionEvent($thesis, 'unavailable'));
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } finally {
            $this->releaseLock($lockKey, $lockId);
        }
    }
    
    /**
     * Release a distributed lock
     *
     * @param string $lockKey
     * @param string $lockId
     * @return bool
     */
    private function releaseLock($lockKey, $lockId)
    {
        $script = "
            if redis.call('get', KEYS[1]) == ARGV[1] then
                return redis.call('del', KEYS[1])
            else
                return 0
            end
        ";
        
        return Redis::eval($script, 1, $lockKey, $lockId);
    }
}