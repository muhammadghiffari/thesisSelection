<?php

namespace App\Livewire\Student;

use App\Models\Student;
use App\Models\ThesisTitle;
use Livewire\Component;

class ThesisSelectionForm extends Component
{
    public $step = 1;
    public $students = [];
    public $selectedStudent = null;
    public $studentClass = null;
    public $studentTopic = null;
    public $token = '';
    public $npm = '';
    public $email = '';
    public $thesisTitles = [];
    public $selectedThesisTitle = null;

    public $error = null;
    public $success = null;

    protected $rules = [
        'selectedStudent'     => 'required',
        'token'               => 'required|min:5|max:5',
        'npm'                 => 'required|min:10|max:10',
        'email'               => 'required|email:rfc,dns|ends_with:ui.ac.id',
        'selectedThesisTitle' => 'required',
    ];

    protected $messages = [
        'selectedStudent.required'     => 'Pilih nama mahasiswa terlebih dahulu.',
        'token.required'               => 'Token harus diisi.',
        'token.min'                    => 'Token harus 5 karakter.',
        'token.max'                    => 'Token harus 5 karakter.',
        'npm.required'                 => 'NPM harus diisi.',
        'npm.min'                      => 'NPM harus 10 digit.',
        'npm.max'                      => 'NPM harus 10 digit.',
        'email.required'               => 'Email harus diisi.',
        'email.email'                  => 'Format email tidak valid.',
        'email.ends_with'              => 'Email harus menggunakan domain @ui.ac.id',
        'selectedThesisTitle.required' => 'Pilih judul skripsi terlebih dahulu.',
    ];

    public function mount()
    {
        $this->students = Student::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.student.thesis-selection-form')
            ->layout('layouts.guest');
    }

    public function updatedSelectedStudent()
    {
        if ($this->selectedStudent) {
            $student = Student::find($this->selectedStudent);
            if ($student) {
                $this->studentClass = $student->class;
                $this->studentTopic = $student->thesis_topic;

                // Cek apakah mahasiswa sudah memilih judul
                if ($student->has_selected && !$student->has_reset) {
                    $this->error = 'Mahasiswa ini sudah memilih judul skripsi. Hubungi admin untuk reset.';
                } else {
                    $this->error = null;
                }
            }
        }
    }

    public function continueToStep2()
    {
        $this->validate([
            'selectedStudent' => 'required',
            'token'           => 'required|min:5|max:5',
            'npm'             => 'required|min:10|max:10',
            'email'           => 'required|email:rfc,dns|ends_with:ui.ac.id',
        ]);

        // Validasi token sesuai dengan mahasiswa yang dipilih
        $student = Student::find($this->selectedStudent);
        if (!$student) {
            $this->error = 'Mahasiswa tidak ditemukan.';
            return;
        }

        if ($student->token !== $this->token) {
            $this->error = 'Token tidak valid.';
            return;
        }

        if ($student->npm !== $this->npm) {
            $this->error = 'NPM tidak sesuai.';
            return;
        }

        if ($student->email !== $this->email) {
            $this->error = 'Email tidak sesuai.';
            return;
        }

        // Ambil judul skripsi berdasarkan topik mahasiswa
        $this->thesisTitles = ThesisTitle::where('topic', $student->thesis_topic)
            ->where('status', 'Available')
            ->get();

        if ($this->thesisTitles->isEmpty()) {
            $this->error = 'Tidak ada judul skripsi yang tersedia untuk topik ini.';
            return;
        }

        $this->error = null;
        $this->step = 2;
    }

    public function continueToStep3()
    {
        $this->validate([
            'selectedThesisTitle' => 'required',
        ]);

        $thesisTitle = ThesisTitle::find($this->selectedThesisTitle);
        if (!$thesisTitle) {
            $this->error = 'Judul skripsi tidak ditemukan.';
            return;
        }

        if ($thesisTitle->status !== 'Available') {
            $this->error = 'Judul skripsi ini sudah tidak tersedia.';
            return;
        }

        $this->error = null;
        $this->step = 3;
    }

    public function confirmSelection()
    {
        $student = Student::find($this->selectedStudent);
        $thesisTitle = ThesisTitle::find($this->selectedThesisTitle);

        if (!$student || !$thesisTitle) {
            $this->error = 'Data tidak valid.';
            return;
        }

        try {
            // Mulai transaksi database
            \DB::beginTransaction();

            // Update status judul skripsi
            $thesisTitle->status = 'In Selection';
            $thesisTitle->save();

            // Buat entri pemilihan skripsi
            $selection = $student->thesisSelection()->create([
                'thesis_title_id' => $thesisTitle->id,
                'ip_address'      => request()->ip(),
                'status'          => 'Pending'
            ]);

            // Update status mahasiswa
            $student->has_selected = true;
            $student->save();

            // Log aktivitas
            $student->activityLogs()->create([
                'action'      => 'thesis_selection',
                'description' => 'Mahasiswa memilih judul skripsi: ' . $thesisTitle->title,
                'ip_address'  => request()->ip()
            ]);

            // Broadcast ke admin (jika menggunakan Pusher)
            event(new ThesisEvents($selection));

            // Kirim email konfirmasi (bisa dijalankan di background job)
            // \App\Jobs\SendSelectionConfirmationEmail::dispatch($student, $thesisTitle);

            \DB::commit();

            $this->step = 4;
            $this->success = 'Pemilihan judul skripsi berhasil. Silakan cek email Anda untuk konfirmasi.';
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function resetForm()
    {
        $this->reset(['step', 'selectedStudent', 'studentClass', 'studentTopic', 'token', 'npm', 'email', 'thesisTitles', 'selectedThesisTitle', 'error', 'success']);
        $this->students = Student::orderBy('name')->get();
    }

    public function goBack()
    {
        $this->step--;
    }
}