<?php
// app/Http/Livewire/Student/ThesisSelectionForm.php

namespace App\Livewire\Student;

use App\Models\Student;
use App\Models\ThesisTitle;
use App\Models\ThesisSelection;
use App\Models\ActivityLog;
use App\Models\LiveActivity;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ThesisSelectionForm extends Component
{
    public $step = 1;
    
    // Student Info
    public $selectedStudentId = null;
    public $studentName = '';
    public $studentClass = '';
    public $studentTopic = '';
    public $studentNpm = '';
    public $studentEmail = '';
    public $token = '';
    
    // Thesis Selection
    public $selectedThesisId = null;
    public $availableTheses = [];
    
    // Loading states
    public $loading = false;
    public $searchingStudent = false;
    
    // Error and success messages
    public $errorMessage = '';
    public $successMessage = '';
    
    // Listeners for real-time updates
    protected $listeners = ['thesisStatusUpdated' => 'refreshThesesList'];
    
    public function mount()
    {
        $this->resetForm();
    }
    
    public function updatedSelectedStudentId()
    {
        $this->searchingStudent = true;
        
        if ($this->selectedStudentId) {
            $student = Student::find($this->selectedStudentId);
            
            if ($student) {
                $this->studentName = $student->name;
                $this->studentClass = $student->class;
                $this->studentTopic = $student->topic;
                $this->token = $student->token ?? '';
                $this->studentNpm = $student->npm ?? '';
                $this->studentEmail = $student->email ?? '';
                
                // Check if student already has a selection
                $existingSelection = ThesisSelection::where('student_id', $student->id)
                    ->where('status', 'selected')
                    ->first();
                
                if ($existingSelection) {
                    $this->errorMessage = 'Anda sudah memilih judul skripsi sebelumnya. Jika ingin mengubah pilihan, silakan hubungi admin.';
                } else {
                    $this->errorMessage = '';
                }
            }
        } else {
            $this->resetStudentInfo();
        }
        
        $this->searchingStudent = false;
    }
    
    public function goToStep($step)
    {
        if ($step == 2) {
            $validData = $this->validateStudentInfo();
            if (!$validData) {
                return;
            }
            
            // Log activity
            if ($this->selectedStudentId) {
                $this->logActivity(
                    'student', 
                    $this->selectedStudentId, 
                    'form_step_1_completed', 
                    'Student proceeded to thesis selection step'
                );
                
                // Load available theses based on student topic
                $this->loadAvailableTheses();
            }
        }
        
        if ($step == 3 && !$this->selectedThesisId) {
            $this->addError('selectedThesisId', 'Anda harus memilih judul skripsi terlebih dahulu');
            return;
        }
        
        if ($step == 3) {
            // Log activity
            $this->logActivity(
                'thesis_title', 
                $this->selectedThesisId, 
                'form_step_2_completed', 
                'Student selected thesis title'
            );
        }
        
        $this->step = $step;
    }
    
    public function validateStudentInfo()
    {
        $this->errorMessage = '';
        
        $rules = [
            'selectedStudentId' => 'required|exists:students,id',
            'token' => [
                'required',
                'size:5',
                'alpha_num',
                Rule::exists('students', 'token')->where(function ($query) {
                    return $query->where('id', $this->selectedStudentId);
                }),
            ],
            'studentNpm' => [
                'required',
                'regex:/^[0-9]{10}$/',
                Rule::exists('students', 'npm')->where(function ($query) {
                    return $query->where('id', $this->selectedStudentId);
                }),
            ],
            'studentEmail' => [
                'required',
                'email',
                'regex:/^.+@ui\.ac\.id$/',
                Rule::exists('students', 'email')->where(function ($query) {
                    return $query->where('id', $this->selectedStudentId);
                }),
            ],
        ];
        
        $messages = [
            'selectedStudentId.required' => 'Silakan pilih nama mahasiswa',
            'selectedStudentId.exists' => 'Mahasiswa tidak ditemukan dalam sistem',
            'token.required' => 'Token diperlukan',
            'token.size' => 'Token harus terdiri dari 5 karakter',
            'token.alpha_num' => 'Token hanya boleh berisi huruf dan angka',
            'token.exists' => 'Token tidak valid untuk mahasiswa yang dipilih',
            'studentNpm.required' => 'NPM diperlukan',
            'studentNpm.regex' => 'Format NPM tidak valid. NPM harus terdiri dari 10 digit angka',
            'studentNpm.exists' => 'NPM tidak valid untuk mahasiswa yang dipilih',
            'studentEmail.required' => 'Email diperlukan',
            'studentEmail.email' => 'Format email tidak valid',
            'studentEmail.regex' => 'Email harus menggunakan domain @ui.ac.id',
            'studentEmail.exists' => 'Email tidak valid untuk mahasiswa yang dipilih',
        ];
        
        $validator = Validator::make([
            'selectedStudentId' => $this->selectedStudentId,
            'token' => $this->token,
            'studentNpm' => $this->studentNpm,
            'studentEmail' => $this->studentEmail,
        ], $rules, $messages);
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->errorMessage .= $error . '<br>';
            }
            return false;
        }
        
        return true;
    }
    
    public function loadAvailableTheses()
    {
        $student = Student::find($this->selectedStudentId);
        
        if ($student) {
            // Get all thesis titles matching the student's topic
            $this->availableTheses = ThesisTitle::where('topic', $student->topic)
                ->orderBy('title')
                ->get()
                ->map(function ($thesis) {
                    // Check how many students have selected this thesis
                    $selectionsCount = ThesisSelection::where('thesis_title_id', $thesis->id)
                        ->where('status', 'selected')
                        ->count();
                    
                    $status = 'Available';
                    if ($selectionsCount >= 1) {
                        $status = 'In Selection';
                    }
                    if ($selectionsCount >= 2) {
                        $status = 'Unavailable';
                    }
                    
                    return [
                        'id' => $thesis->id,
                        'title' => $thesis->title,
                        'description' => $thesis->description,
                        'topic' => $thesis->topic,
                        'status' => $status,
                        'disabled' => $status === 'Unavailable',
                    ];
                })
                ->toArray();
        }
    }
    
    public function selectThesis($thesisId)
    {
        $this->selectedThesisId = $thesisId;
        
        // Create a live activity
        LiveActivity::create([
            'student_id' => $this->selectedStudentId,
            'thesis_title_id' => $thesisId,
            'action' => 'viewing_thesis',
            'ip_address' => request()->ip(),
        ]);
        
        // Emit event for real-time updates
        $this->emit('liveActivityCreated', $this->selectedStudentId, $thesisId, 'viewing_thesis');
    }
    
    public function submitSelection()
    {
        $this->loading = true;
        $this->errorMessage = '';
        
        try {
            // Verify this thesis is still available
            $selectionsCount = ThesisSelection::where('thesis_title_id', $this->selectedThesisId)
                ->where('status', 'selected')
                ->count();
            
            if ($selectionsCount >= 2) {
                $this->errorMessage = 'Maaf, judul skripsi ini sudah tidak tersedia. Silakan pilih judul lain.';
                $this->goToStep(2);
                $this->loadAvailableTheses();
                $this->loading = false;
                return;
            }
            
            // Check if student already has a selection
            $existingSelection = ThesisSelection::where('student_id', $this->selectedStudentId)
                ->first();
            
            if ($existingSelection) {
                // Update existing selection
                $existingSelection->update([
                    'thesis_title_id' => $this->selectedThesisId,
                    'ip_address' => request()->ip(),
                    'status' => 'selected',
                ]);
                
                $selection = $existingSelection;
            } else {
                // Create new selection
                $selection = ThesisSelection::create([
                    'student_id' => $this->selectedStudentId,
                    'thesis_title_id' => $this->selectedThesisId,
                    'ip_address' => request()->ip(),
                    'status' => 'selected',
                ]);
            }
            
            // Log activity
            $this->logActivity(
                'thesis_selection', 
                $selection->id, 
                'thesis_selected', 
                'Student confirmed thesis selection'
            );
            
            // Create a live activity
            LiveActivity::create([
                'student_id' => $this->selectedStudentId,
                'thesis_title_id' => $this->selectedThesisId,
                'action' => 'selected_thesis',
                'ip_address' => request()->ip(),
            ]);
            
            // Emit event for real-time updates
            $this->emit('thesisSelected', $this->selectedStudentId, $this->selectedThesisId);
            
            // Send confirmation email
            $this->sendConfirmationEmail();
            
            $this->successMessage = 'Pilihan judul skripsi Anda telah berhasil disimpan. Silakan cek email untuk konfirmasi.';
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
        
        $this->loading = false;
    }
    
    public function sendConfirmationEmail()
    {
        $student = Student::find($this->selectedStudentId);
        $thesis = ThesisTitle::find($this->selectedThesisId);
        
        // Here you'd implement the email sending logic
        // You can use Laravel's built-in Mail facade
        /*
        Mail::to($student->email)->send(new ThesisSelectionConfirmation(
            $student,
            $thesis
        ));
        */
    }
    
    public function refreshThesesList()
    {
        if ($this->step == 2) {
            $this->loadAvailableTheses();
        }
    }
    
    public function resetForm()
    {
        $this->step = 1;
        $this->selectedStudentId = null;
        $this->resetStudentInfo();
        $this->selectedThesisId = null;
        $this->availableTheses = [];
        $this->errorMessage = '';
        $this->successMessage = '';
    }
    
    public function resetStudentInfo()
    {
        $this->studentName = '';
        $this->studentClass = '';
        $this->studentTopic = '';
        $this->studentNpm = '';
        $this->studentEmail = '';
        $this->token = '';
    }
    
    private function logActivity($type, $id, $action, $description)
    {
        ActivityLog::create([
            'loggable_type' => $type,
            'loggable_id' => $id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
    
    public function render()
    {
    return view('livewire.student.thesis-selection-form', [
        'students' => Student::orderBy('name')->get(),
    ])->layout('layouts.guest');
}

}