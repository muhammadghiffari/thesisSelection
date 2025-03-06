<?php

namespace App\Livewire\Student;

use App\Models\Student;
use App\Models\ThesisTitle;
use App\Models\ThesisSelection;
use App\Events\ThesisSelectionEvent;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
    public $thesisTitlesStatus = [];
    public $countdowns = [];

    public $error = null;
    public $success = null;

    protected $listeners = [
        'echo:thesis-selections,thesis.update' => 'handleThesisUpdate',
        'countdownEnded'                       => 'handleCountdownEnded',
    ];

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

        // Ambil judul skripsi berdasarkan topik mahasiswa - termasuk In Selection dengan timer
        $this->loadThesisTitles($student->thesis_topic);

        if ($this->thesisTitles->isEmpty()) {
            $this->error = 'Tidak ada judul skripsi yang tersedia untuk topik ini.';
            return;
        }

        $this->error = null;
        $this->step = 2;
    }

    protected function loadThesisTitles($topic)
    {
        // Load semua judul skripsi termasuk yang 'In Selection' (sedang dalam timer)
        $this->thesisTitles = ThesisTitle::where('topic', $topic)
            ->whereIn('status', ['Available', 'In Selection'])
            ->get();

        // Periksa status real-time dari database untuk memastikan data terbaru
        $this->refreshThesisTitlesStatus();
    }

    protected function refreshThesisTitlesStatus()
    {
        $this->thesisTitlesStatus = [];
        $this->countdowns = [];

        foreach ($this->thesisTitles as $thesis) {
            // Periksa apakah ada pemilihan aktif dengan timer yang belum habis
            $activeSelection = ThesisSelection::where('thesis_title_id', $thesis->id)
                ->where('status', 'Pending')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if ($activeSelection) {
                $this->thesisTitlesStatus[$thesis->id] = 'In Selection';
                $this->countdowns[$thesis->id] = $activeSelection->expires_at->timestamp;
            } else {
                // Jika tidak ada pemilihan aktif, atau timer sudah expired, status 'Available'
                $this->thesisTitlesStatus[$thesis->id] = 'Available';
            }
        }
    }

    public function handleThesisUpdate($event)
    {
        // Update status judul skripsi berdasarkan event real-time
        if (isset($event['id']) && $this->step == 2) {
            $thesisId = $event['id'];

            if ($event['action'] == 'selected') {
                $this->thesisTitlesStatus[$thesisId] = 'In Selection';
                $this->countdowns[$thesisId] = $event['expiresAt'];

                // Jika judul yang dipilih user saat ini menjadi tidak tersedia
                if ($this->selectedThesisTitle == $thesisId) {
                    $this->selectedThesisTitle = null;
                }
            } elseif ($event['action'] == 'expired' || $event['action'] == 'available') {
                $this->thesisTitlesStatus[$thesisId] = 'Available';
                unset($this->countdowns[$thesisId]);
            }

            // Refresh tampilan
            $this->dispatch('refreshCountdowns');
        }
    }

    public function handleCountdownEnded($thesisId)
    {
        // Ketika timer habis, update status di database dan broadcast
        try {
            DB::beginTransaction();

            $thesis = ThesisTitle::find($thesisId);
            if ($thesis && $thesis->status == 'In Selection') {
                // Update status judul menjadi Available
                $thesis->status = 'Available';
                $thesis->save();

                // Batalkan pemilihan yang pending dengan thesis_id ini
                ThesisSelection::where('thesis_title_id', $thesisId)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Expired']);

                // Broadcast ke seluruh user
                event(new ThesisSelectionEvent($thesis, 'expired'));

                // Update local state
                $this->thesisTitlesStatus[$thesisId] = 'Available';
                unset($this->countdowns[$thesisId]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = "Error: " . $e->getMessage();
        }
    }

    public function continueToStep3()
    {
        $this->validate([
            'selectedThesisTitle' => 'required',
        ]);

        $thesisId = $this->selectedThesisTitle;
        $status = $this->thesisTitlesStatus[$thesisId] ?? null;

        if (!$status || $status !== 'Available') {
            $this->error = 'Judul skripsi ini sedang dipilih oleh mahasiswa lain atau tidak tersedia.';
            $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
            return;
        }

        // Mulai timer untuk pemilihan ini
        try {
            DB::beginTransaction();

            $thesis = ThesisTitle::find($thesisId);
            $thesis->status = 'In Selection';
            $thesis->save();

            // Simpan temporary selection dengan waktu kedaluwarsa
            $expiresAt = now()->addMinute();
            $selection = new ThesisSelection([
                'student_id'      => $this->selectedStudent,
                'thesis_title_id' => $thesisId,
                'ip_address'      => request()->ip(),
                'status'          => 'Pending',
                'expires_at'      => $expiresAt
            ]);
            $selection->save();

            // Broadcast ke semua user
            event(new ThesisSelectionEvent($thesis, 'selected', $expiresAt));

            // Update local state
            $this->thesisTitlesStatus[$thesisId] = 'In Selection';
            $this->countdowns[$thesisId] = $expiresAt->timestamp;

            DB::commit();

            $this->error = null;
            $this->step = 3;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = "Error: " . $e->getMessage();
            $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
        }
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
            DB::beginTransaction();

            // Periksa apakah timer masih berlaku
            $activeSelection = ThesisSelection::where('thesis_title_id', $thesisTitle->id)
                ->where('student_id', $student->id)
                ->where('status', 'Pending')
                ->where('expires_at', '>', now())
                ->first();

            if (!$activeSelection) {
                $this->error = 'Waktu pemilihan judul skripsi telah habis. Silakan pilih judul lain.';
                $this->step = 2;
                $this->loadThesisTitles($student->thesis_topic);
                DB::rollBack();
                return;
            }

            // Update status judul skripsi menjadi Unavailable
            $thesisTitle->status = 'Unavailable';
            $thesisTitle->save();

            // Update status selection menjadi Approved
            $activeSelection->status = 'Approved';
            $activeSelection->save();

            // Update status mahasiswa
            $student->has_selected = true;
            $student->save();

            // Log aktivitas
            $student->activityLogs()->create([
                'action'      => 'thesis_selection',
                'description' => 'Mahasiswa memilih judul skripsi: ' . $thesisTitle->title,
                'ip_address'  => request()->ip()
            ]);

            // Broadcast ke admin dan semua user bahwa judul ini sudah tidak tersedia lagi
            event(new ThesisSelectionEvent($thesisTitle, 'unavailable'));

            // Kirim email konfirmasi (bisa dijalankan di background job)
            // \App\Jobs\SendSelectionConfirmationEmail::dispatch($student, $thesisTitle);

            DB::commit();

            $this->step = 4;
            $this->success = 'Pemilihan judul skripsi berhasil. Silakan cek email Anda untuk konfirmasi.';
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function resetForm()
    {
        $this->reset(['step', 'selectedStudent', 'studentClass', 'studentTopic', 'token', 'npm', 'email', 'thesisTitles', 'selectedThesisTitle', 'error', 'success', 'thesisTitlesStatus', 'countdowns']);
        $this->students = Student::orderBy('name')->get();
    }

    public function goBack()
    {
        $this->step--;

        if ($this->step == 2) {
            // Refresh thesis titles when going back to step 2
            $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
        }
    }
}