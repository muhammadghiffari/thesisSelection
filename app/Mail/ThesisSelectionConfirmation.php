<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThesisSelectionConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $thesisTitle;

    public function __construct($student, $thesisTitle)
    {
        $this->student = $student;
        $this->thesisTitle = $thesisTitle;
    }

    public function build()
    {
        return $this->markdown('emails.thesis-selection.confirmation')
            ->subject('Konfirmasi Pemilihan Judul Skripsi');
    }
}
