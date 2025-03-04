<?php

namespace App\Livewire\Student;

use App\Models\Student;
use App\Models\ThesisTitle;
use App\Models\ThesisSelection;
use App\Models\LiveActivity;
use App\Models\ActivityLog;
use App\Events\ThesisSelected;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThesisSelectionConfirmation;


class ThesisSelectionForm extends Component
{
    public $studentName;
    public $npm;
    public $token;
    public $email;
    public $student;
    public $selectedThesisId;
    public $thesisTopics = [];
    public $availableThesisTitles = [];
    public $confirmationStep = false;
    public $success = false;
    public $selectedThesisTitle;


    protected $rules = [
        'studentName'      => 'required',
        'npm'              => 'required',
        'token'            => 'required|size:5',
        'email'            => 'required|email|ends_with:ui.ac.id',
        'selectedThesisId' => 'required|exists:thesis_titles,id',
    ];

    public function mount()
    {
        $this->thesisTopics = ThesisTitle::select('topic')->distinct()->pluck('topic')->toArray();
    }

    public function render()
    {
        return view('livewire.student.thesis-selection-form')
            ->layout('layouts.guest');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if ($propertyName === 'studentName' && !empty($this->studentName)) {
            $this->loadStudentData();
        }

        if ($propertyName === 'student.thesis_topic') {
            $this->loadAvailableThesisTitles();
        }
    }

    public function loadStudentData()
    {
        $this->student = Student::where('name', $this->studentName)->first();

        if ($this->student) {
            $this->loadAvailableThesisTitles();
            // Record live activity
            LiveActivity::create([
                'student_id' => $this->student->id,
                'action'     => 'Selected name: ' . $this->studentName,
                'ip_address' => request()->ip()
            ]);
        }
    }

    public function loadAvailableThesisTitles()
    {
        if ($this->student) {
            $this->availableThesisTitles = ThesisTitle::where('topic', $this->student->thesis_topic)
                ->where('status', 'Available')
                ->get();

            // Record live activity
            LiveActivity::create([
                'student_id' => $this->student->id,
                'action'     => 'Loaded thesis titles for topic: ' . $this->student->thesis_topic,
                'ip_address' => request()->ip()
            ]);
        }
    }

    public function validateStudent()
    {
        $this->validate([
            'studentName' => 'required',
            'npm'         => 'required',
            'token'       => 'required|size:5',
            'email'       => 'required|email|ends_with:ui.ac.id',
        ]);

        $student = Student::where('name', $this->studentName)
            ->where('npm', $this->npm)
            ->where('token', strtoupper($this->token))
            ->where('email', $this->email)
            ->first();

        if (!$student) {
            $this->addError('token', 'Invalid student information. Please check your details.');
            return false;
        }

        if ($student->has_selected && !$student->has_reset) {
            $this->addError('token', 'You have already selected a thesis title. Please contact admin if you need to change.');
            return false;
        }

        $this->student = $student;
        $this->loadAvailableThesisTitles();

        // Record activity log
        ActivityLog::create([
            'loggable_type' => Student::class,
            'loggable_id'   => $student->id,
            'action'        => 'Validated student information',
            'description'   => 'Student validated their information successfully',
            'ip_address'    => request()->ip()
        ]);

        return true;
    }

    public function selectThesisTitle()
    {
        if (!$this->validateStudent()) {
            return;
        }

        $this->validate([
            'selectedThesisId' => 'required|exists:thesis_titles,id'
        ]);

        // Check if thesis is still available (might have been selected by someone else)
        $thesisTitle = ThesisTitle::where('id', $this->selectedThesisId)
            ->where('status', 'Available')
            ->first();

        if (!$thesisTitle) {
            $this->addError('selectedThesisId', 'This thesis title is no longer available. Please select another one.');
            $this->loadAvailableThesisTitles(); // Reload available titles
            return;
        }

        // Everything is valid, proceed to confirmation step
        $this->selectedThesisTitle = $thesisTitle;
        $this->confirmationStep = true;

        // Record live activity
        LiveActivity::create([
            'student_id'      => $this->student->id,
            'thesis_title_id' => $thesisTitle->id,
            'action'          => 'Selected thesis: ' . $thesisTitle->title,
            'ip_address'      => request()->ip()
        ]);
    }

    public function confirmSelection()
    {
        if (!$this->student || !$this->selectedThesisTitle) {
            return redirect()->route('student.thesis-selection');
        }

        // Check again if the thesis is still available
        $thesisTitle = ThesisTitle::where('id', $this->selectedThesisTitle->id)
            ->where('status', 'Available')
            ->first();

        if (!$thesisTitle) {
            $this->addError('selectedThesisId', 'This thesis title is no longer available. Please select another one.');
            $this->confirmationStep = false;
            $this->loadAvailableThesisTitles();
            return;
        }

        // Update thesis title status
        $thesisTitle->update(['status' => 'In Selection']);

        // Create thesis selection record
        $selection = ThesisSelection::create([
            'student_id'      => $this->student->id,
            'thesis_title_id' => $thesisTitle->id,
            'ip_address'      => request()->ip(),
            'status'          => 'Pending'
        ]);

        // Update student status
        $this->student->update([
            'has_selected' => true
        ]);

        // Record activity log
        ActivityLog::create([
            'loggable_type' => ThesisSelection::class,
            'loggable_id'   => $selection->id,
            'action'        => 'Thesis selection confirmed',
            'description'   => 'Student selected thesis title: ' . $thesisTitle->title,
            'ip_address'    => request()->ip()
        ]);

        // Send confirmation email
        Mail::to($this->student->email)->send(new ThesisSelectionConfirmation($this->student, $thesisTitle));

        // Broadcast event for real-time updates
        event(new ThesisSelected($selection));

        // Show success message
        $this->success = true;
    }

    public function startOver()
    {
        $this->reset([
            'studentName',
            'npm',
            'token',
            'email',
            'selectedThesisId',
            'confirmationStep',
            'success',
            'selectedThesisTitle'
        ]);
        $this->availableThesisTitles = [];
    }
}
