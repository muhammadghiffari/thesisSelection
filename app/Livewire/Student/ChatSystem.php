<?php

namespace App\Livewire\Student;

use App\Models\Chat;
use App\Models\Student;
use App\Models\ThesisSelection;
use App\Events\NewChatMessage;
use Livewire\Component;

class ChatSystem extends Component
{
    public $studentId;
    public $token;
    public $studentName;
    public $messages = [];
    public $newMessage = '';
    public $isAuthenticated = false;
    public $thesisSelection;

    protected $listeners = [
        'echo-private:chat.student.*,new-message' => 'handleNewMessage',
    ];

    public function mount()
    {
        // Check for existing session
        if (session()->has('student_id') && session()->has('token')) {
            $this->studentId = session()->get('student_id');
            $this->token = session()->get('token');
            $this->authenticateStudent();
        }
    }

    public function authenticateStudent()
    {
        $this->validate([
            'studentName' => 'required_without:studentId',
            'token'       => 'required|size:5',
        ]);

        try {
            // Find student by name or ID
            if ($this->studentId) {
                $student = Student::where('id', $this->studentId)
                    ->where('token', $this->token)
                    ->first();
            } else {
                $student = Student::where('name', $this->studentName)
                    ->where('token', $this->token)
                    ->first();
            }

            if ($student) {
                $this->studentId = $student->id;
                $this->studentName = $student->name;

                // Store in session
                session(['student_id' => $student->id, 'token' => $this->token]);

                // Get latest thesis selection
                $this->thesisSelection = ThesisSelection::where('student_id', $student->id)
                    ->with('thesisTitle')
                    ->latest()
                    ->first();

                $this->isAuthenticated = true;
                $this->loadMessages();
            } else {
                session()->flash('error', 'Nama atau token tidak valid');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function loadMessages()
    {
        if ($this->studentId) {
            $this->messages = Chat::where('student_id', $this->studentId)
                ->with(['student', 'user'])
                ->orderBy('created_at', 'asc')
                ->get();
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|min:1',
        ]);

        // Create new chat message
        $chat = Chat::create([
            'student_id'          => $this->studentId,
            'user_id'             => null,
            'message'             => $this->newMessage,
            'sender_type'         => 'student',
            'is_read'             => false,
            'thesis_selection_id' => $this->thesisSelection ? $this->thesisSelection->id : null,
        ]);

        // Broadcast the message
        broadcast(new NewChatMessage($chat))->toOthers();

        // Add message to the list
        $this->messages->push($chat);

        // Clear input
        $this->newMessage = '';
    }

    public function handleNewMessage($event)
    {
        // If the message is for this student, add it to messages
        if ($event['student_id'] == $this->studentId) {
            $this->messages->push(Chat::find($event['id']));
        }
    }

    public function logout()
    {
        session()->forget(['student_id', 'token']);
        $this->reset(['studentId', 'token', 'studentName', 'messages', 'isAuthenticated', 'thesisSelection']);
    }

    public function render()
    {
        return view('livewire.student.chat-system')
            ->layout('layouts.guest');
    }
}