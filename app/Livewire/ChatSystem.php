<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\User;
use Livewire\Component;

class ChatSystem extends Component
{
    public $messages = [];
    public $newMessage = '';
    public $student = null;
    public $user = null;

    protected $listeners = [
        'echo:chat,ChatMessageEvent' => 'handleNewMessage',
        'loadChat'                   => 'loadChat'
    ];

    public function mount($studentId = null)
    {
        $this->user = auth()->user();

        if ($studentId) {
            $this->student = Student::find($studentId);
            $this->loadChat();
        }
    }

    public function render()
    {
        return view('livewire.chat-system');
    }

    public function loadChat($studentId = null)
    {
        if ($studentId) {
            $this->student = Student::find($studentId);
        }

        if ($this->student) {
            // Load messages between admin and this student
            $this->messages = ChatMessage::where(function ($query) {
                $query->where('student_id', $this->student->id)
                    ->where('user_id', $this->user->id);
            })->orWhere(function ($query) {
                $query->where('student_id', $this->student->id)
                    ->whereNull('user_id');
            })->orderBy('created_at', 'asc')->get();
        }
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) || !$this->student) {
            return;
        }

        $message = new ChatMessage([
            'user_id'    => $this->user->id,
            'student_id' => $this->student->id,
            'message'    => $this->newMessage,
            'is_read'    => false
        ]);

        $message->save();

        // Broadcast the message using Pusher
        event(new \App\Events\ChatMessageEvent($message));

        // Also log this activity
        $this->user->activityLogs()->create([
            'action'      => 'chat_message',
            'description' => 'Message sent to student: ' . $this->student->name,
            'ip_address'  => request()->ip()
        ]);

        $this->newMessage = '';
        $this->loadChat();
    }

    public function handleNewMessage($event)
    {
        // Only reload if the message is for the current chat
        if ($event['message']['student_id'] == $this->student->id) {
            $this->loadChat();
        }

        // If the message is from a student to this admin, mark as unread in the notification
        if (!isset($event['message']['user_id']) && $this->student->id == $event['message']['student_id']) {
            // Trigger a notification on the admin side
            $this->dispatch('newMessageNotification', [
                'studentId'   => $this->student->id,
                'studentName' => $this->student->name
            ]);
        }
    }

    public function markAsRead()
    {
        if ($this->student) {
            ChatMessage::where('student_id', $this->student->id)
                ->whereNull('user_id')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }
}