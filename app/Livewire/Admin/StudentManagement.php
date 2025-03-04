<?php

namespace App\Http\Livewire\Admin;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TokenNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;

class StudentManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $name;
    public $npm;
    public $class;
    public $thesis_topic;
    public $email;
    public $token;
    public $search = '';
    public $classFilter = '';
    public $topicFilter = '';
    public $studentImport;
    public $selectedStudentId;
    public $isEditing = false;
    public $showTokenModal = false;
    public $showImportModal = false;
    public $showDeleteModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'npm' => 'required|string|max:20|unique:students,npm',
        'class' => 'required|string|max:50',
        'thesis_topic' => 'required|string|max:100',
        'email' => 'required|email|ends_with:ui.ac.id|unique:students,email',
    ];

    public function mount()
    {
        $this->token = $this->generateToken();
    }

    public function render()
    {
        $students = Student::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('npm', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->classFilter, function ($query) {
                $query->where('class', $this->classFilter);
            })
            ->when($this->topicFilter, function ($query) {
                $query->where('thesis_topic', $this->topicFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        $classes = Student::select('class')->distinct()->pluck('class')->toArray();
        $topics = Student::select('thesis_topic')->distinct()->pluck('thesis_topic')->toArray();

        return view('livewire.admin.student-management', [
            'students' => $students,
            'classes' => $classes,
            'topics' => $topics,
        ]);
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->rules['npm'] = 'required|string|max:20|unique:students,npm,' . $this->selectedStudentId;
            $this->rules['email'] = 'required|email|ends_with:ui.ac.id|unique:students,email,' . $this->selectedStudentId;
        }

        $this->validate();

        if ($this->isEditing) {
            $student = Student::find($this->selectedStudentId);
            $student->update([
                'name' => $this->name,
                'npm' => $this->npm,
                'class' => $this->class,
                'thesis_topic' => $this->thesis_topic,
                'email' => $this->email,
            ]);

            $this->resetForm();
            session()->flash('message', 'Student updated successfully!');
        } else {
            Student::create([
                'name' => $this->name,
                'npm' => $this->npm,
                'class' => $this->class,
                'thesis_topic' => $this->thesis_topic,
                'email' => $this->email,
                'token' => $this->token,
            ]);

            $this->resetForm();
            session()->flash('message', 'Student added successfully!');
        }
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $this->selectedStudentId = $id;
        $student = Student::find($id);

        $this->name = $student->name;
        $this->npm = $student->npm;
        $this->class = $student->class;
        $this->thesis_topic = $student->thesis_topic;
        $this->email = $student->email;
        $this->token = $student->token;
    }

    public function delete()
    {
        $student = Student::find($this->selectedStudentId);
        $student->delete();

        $this->resetForm();
        $this->showDeleteModal = false;
        session()->flash('message', 'Student deleted successfully!');
    }

    public function confirmDelete($id)
    {
        $this->selectedStudentId = $id;
        $this->showDeleteModal = true;
    }

    public function resetSelection($id)
    {
        $student = Student::find($id);
        $student->update([
            'has_selected' => false,
            'has_reset' => true,
        ]);

        if ($student->thesisSelection) {
            $thesisTitle = $student->thesisSelection->thesisTitle;
            $thesisTitle->update(['status' => 'Available']);
            $student->thesisSelection->delete();
        }

        session()->flash('message', 'Student selection has been reset!');
    }

    public function regenerateToken($id)
    {
        $student = Student::find($id);
        $newToken = $this->generateToken();
        $student->update(['token' => $newToken]);

        // Send token to student's email
        Mail::to($student->email)->send(new TokenNotification($student));

        session()->flash('message', 'Token regenerated and sent to student!');
    }

    public function showSendTokenModal($id)
    {
        $this->selectedStudentId = $id;
        $this->showTokenModal = true;
    }

    public function sendToken()
    {
        $student = Student::find($this->selectedStudentId);
        Mail::to($student->email)->send(new TokenNotification($student));

        $this->showTokenModal = false;
        session()->flash('message', 'Token sent to student!');
    }

    public function showImportStudentsModal()
    {
        $this->showImportModal = true;
    }

    public function importStudents()
    {
        $this->validate([
            'studentImport' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new StudentsImport, $this->studentImport);

        $this->showImportModal = false;
        $this->studentImport = null;
        session()->flash('message', 'Students imported successfully!');
    }

    public function exportStudents()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function generateToken()
    {
        return strtoupper(Str::random(5));
    }

    public function resetForm()
    {
        $this->name = '';
        $this->npm = '';
        $this->class = '';
        $this->thesis_topic = '';
        $this->email = '';
        $this->token = $this->generateToken();
        $this->isEditing = false;
        $this->selectedStudentId = null;
    }
}
