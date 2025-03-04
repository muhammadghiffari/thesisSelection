<?php

namespace App\Notifications;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ThesisSelectionCreated extends Notification implements ShouldQueue
{
    use Queueable, InteractsWithSockets;

    public $selection;

    public function __construct($selection)
    {
        $this->selection = $selection;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pemilihan Judul Skripsi Baru')
            ->markdown('notifications.thesis-selection.created');
    }

    public function toArray($notifiable)
    {
        return [
            'student_name' => $this->selection->student->name,
            'thesis_title' => $this->selection->thesisTitle->title,
            'created_at'   => $this->selection->created_at
        ];
    }
}
