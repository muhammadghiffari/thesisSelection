<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ThesisTitle;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send token to student
     * 
     * @param Student $student
     * @param string $token
     * @return bool
     */
    public function sendToken(Student $student, string $token)
    {
        try {
            Mail::send('emails.token', [
                'student' => $student,
                'token'   => $token
            ], function ($message) use ($student) {
                $message->to($student->email)
                    ->subject('Token Pemilihan Judul Skripsi');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send token email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send selection confirmation to student
     * 
     * @param Student $student
     * @param ThesisTitle $thesisTitle
     * @return bool
     */
    public function sendSelectionConfirmation(Student $student, ThesisTitle $thesisTitle)
    {
        try {
            Mail::send('emails.selection-confirmation', [
                'student'     => $student,
                'thesisTitle' => $thesisTitle
            ], function ($message) use ($student) {
                $message->to($student->email)
                    ->subject('Konfirmasi Pemilihan Judul Skripsi');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send selection notification to admin
     * 
     * @param Student $student
     * @param ThesisTitle $thesisTitle
     * @return bool
     */
    public function notifyAdmin(Student $student, ThesisTitle $thesisTitle)
    {
        try {
            Mail::send('emails.admin-notification', [
                'student'     => $student,
                'thesisTitle' => $thesisTitle
            ], function ($message) {
                $message->to(config('thesis.admin_email'))
                    ->subject('Notifikasi Pemilihan Judul Skripsi');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification: ' . $e->getMessage());
            return false;
        }
    }
}