<?php

namespace App\Livewire\Student;

use App\Models\Student;
use App\Models\ThesisTitle;
use App\Models\ThesisSelection;
use App\Events\ThesisSelectionEvent;
use App\Events\ThesisTimerEvent;
use App\Services\ThesisTimerService;
use App\Jobs\ProcessThesisTimerExpiration;
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
        'echo:thesis-timers,timer.update' => 'handleTimerUpdate',
        'echo:thesis-selections,thesis.update' => 'handleThesisUpdate'
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

        // Ambil judul skripsi berdasarkan topik mahasiswa
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
        // Load semua judul skripsi dengan memanfaatkan timer service
        $timerService = app(ThesisTimerService::class);

        $this->thesisTitles = ThesisTitle::where('topic', $topic)
            ->whereIn('status', ['Available', 'In Selection'])
            ->get();

        // Periksa status real-time dan inisialisasi data timer
        $this->refreshThesisTitlesStatus($timerService);
    }

    protected function refreshThesisTitlesStatus($timerService = null)
    {
        if (!$timerService) {
            $timerService = app(ThesisTimerService::class);
        }

        $this->thesisTitlesStatus = [];
        $this->countdowns = [];

        foreach ($this->thesisTitles as $thesis) {
            // Gunakan timer service untuk mendapatkan status timer terbaru
            $timerStatus = $timerService->getTimerStatus($thesis->id);

            if ($timerStatus) {
                // Ada timer aktif
                $this->thesisTitlesStatus[$thesis->id] = 'In Selection';
                $this->countdowns[$thesis->id] = $timerStatus['expires_at'];

                // Jika sedang dipilih oleh mahasiswa ini, maka statusnya 'Selected'
                if ($timerStatus['student_id'] == $this->selectedStudent) {
                    $this->thesisTitlesStatus[$thesis->id] = 'Selected';
                    $this->selectedThesisTitle = $thesis->id;
                }
            } else {
                // Tidak ada timer aktif, status Available
                $this->thesisTitlesStatus[$thesis->id] = 'Available';
            }
        }

        // Dispatch event untuk memperbarui UI timer
        $this->dispatch('refreshCountdowns', ['countdowns' => $this->countdowns]);
    }

    public function handleTimerUpdate($event)
    {
        // Menangani update timer dari WebSocket
        if (isset($event['id']) && $this->step == 2) {
            $thesisId = $event['id'];
            $action = $event['action'];

            switch ($action) {
                case 'started':
                    $this->thesisTitlesStatus[$thesisId] = 'In Selection';
                    $this->countdowns[$thesisId] = $event['data']['expires_at'];

                    // Reset seleksi jika judul yang sedang dipilih tiba-tiba dipilih orang lain
                    if (
                        $this->selectedThesisTitle == $thesisId &&
                        $event['data']['student_id'] != $this->selectedStudent
                    ) {
                        $this->selectedThesisTitle = null;
                    }
                    break;

                case 'expired':
                    $this->thesisTitlesStatus[$thesisId] = 'Available';
                    unset($this->countdowns[$thesisId]);
                    break;

                case 'tick':
                    // Update waktu countdown tanpa mengubah status
                    if (isset($this->countdowns[$thesisId])) {
                        $this->countdowns[$thesisId] = $event['data']['expires_at'];
                    }
                    break;
            }

            // Refresh UI timer
            $this->dispatch('refreshCountdowns', ['countdowns' => $this->countdowns]);
        }
    }

    public function handleThesisUpdate($event)
    {
        // Update status judul berdasarkan perubahan dalam database
        if (isset($event['id']) && ($this->step == 2 || $this->step == 3)) {
            $thesisId = $event['id'];
            $action = $event['action'];

            switch ($action) {
                case 'selected':
                    $this->thesisTitlesStatus[$thesisId] = 'In Selection';
                    if (isset($event['expiresAt'])) {
                        $this->countdowns[$thesisId] = $event['expiresAt'];
                    }
                    break;

                case 'expired':
                case 'available':
                    $this->thesisTitlesStatus[$thesisId] = 'Available';
                    unset($this->countdowns[$thesisId]);
                    break;

                case 'unavailable':
                    $this->thesisTitlesStatus[$thesisId] = 'Unavailable';
                    unset($this->countdowns[$thesisId]);

                    // Jika judul yang dipilih user saat ini menjadi tidak tersedia (diambil mahasiswa lain)
                    if ($this->selectedThesisTitle == $thesisId && $this->step == 3) {
                        // Kembalikan ke step 2 dengan pesan error
                        $this->error = 'Judul skripsi ini telah dipilih oleh mahasiswa lain. Silakan pilih judul lain.';
                        $this->step = 2;
                        $this->selectedThesisTitle = null;
                        $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
                    }
                    break;
            }

            // Refresh UI timer
            $this->dispatch('refreshCountdowns', ['countdowns' => $this->countdowns]);
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

        // Mulai timer menggunakan ThesisTimerService
        $timerService = app(ThesisTimerService::class);
        $timerResult = $timerService->startTimer($thesisId, $this->selectedStudent);

        if (!$timerResult) {
            $this->error = 'Tidak dapat memulai timer pemilihan. Silakan coba lagi atau pilih judul lain.';
            $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
            return;
        }

        // Update local state
        $this->thesisTitlesStatus[$thesisId] = 'Selected'; // Status khusus untuk mahasiswa ini
        $this->countdowns[$thesisId] = $timerResult['expires_at'];

        // Jadwalkan job untuk menangani expiry jika perlu
        ProcessThesisTimerExpiration::dispatch($thesisId)
            ->delay(Carbon::createFromTimestamp($timerResult['expires_at']));

        $this->error = null;
        $this->step = 3;

        // Refresh UI timer
        $this->dispatch('refreshCountdowns', ['countdowns' => $this->countdowns]);
    }

    public function confirmSelection()
    {
        try {
            $timerService = app(ThesisTimerService::class);
            $thesisId = $this->selectedThesisTitle;
            $studentId = $this->selectedStudent;

            // Verifikasi bahwa timer masih aktif
            $timerStatus = $timerService->getTimerStatus($thesisId);

            if (!$timerStatus || $timerStatus['student_id'] != $studentId) {
                $this->error = 'Waktu pemilihan judul skripsi telah habis atau tidak valid. Silakan pilih judul lain.';
                $this->step = 2;
                $this->loadThesisTitles(Student::find($studentId)->thesis_topic);
                return;
            }

            // Selesaikan pemilihan menggunakan service
            $result = $timerService->completeSelection($thesisId, $studentId);

            if (!$result) {
                $this->error = 'Gagal menyelesaikan pemilihan judul. Silakan coba lagi.';
                return;
            }

            // Berhasil
            $this->step = 4;
            $this->success = 'Pemilihan judul skripsi berhasil. Silakan cek email Anda untuk konfirmasi.';

            // // Kirim email notifikasi (dapat diimplementasikan di Queue)
            // dispatch(new \App\Jobs\SendThesisSelectionEmail($studentId, $thesisId));

        } catch (\Exception $e) {
            $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
            // Add debugging information
            $this->error .= ' [Line: ' . $e->getLine() . ' in ' . basename($e->getFile()) . ']';
        }
    }

    public function debugTimerStatus()
    {
        if ($this->selectedThesisTitle) {
            $timerService = app(ThesisTimerService::class);
            $status = $timerService->getTimerStatus($this->selectedThesisTitle);

            return [
                'thesis_id'       => $this->selectedThesisTitle,
                'timer_status'    => $status,
                'local_status'    => $this->thesisTitlesStatus[$this->selectedThesisTitle] ?? 'Unknown',
                'local_countdown' => $this->countdowns[$this->selectedThesisTitle] ?? null,
                'current_time'    => now()->timestamp,
                'time_left'       => $status ? ($status['expires_at'] - now()->timestamp) : null
            ];
        }

        return ['error' => 'No thesis selected'];
    }

    public function resetForm()
    {
        $this->reset(['step', 'selectedStudent', 'studentClass', 'studentTopic', 'token', 'npm', 'email', 'thesisTitles', 'selectedThesisTitle', 'error', 'success', 'thesisTitlesStatus', 'countdowns']);
        $this->students = Student::orderBy('name')->get();
    }

    public function goBack()
    {
        // Jika kembali dari step 3, batalkan pemilihan saat ini jika ada
        if ($this->step == 3 && $this->selectedThesisTitle) {
            $timerService = app(ThesisTimerService::class);
            $timerStatus = $timerService->getTimerStatus($this->selectedThesisTitle);

            if ($timerStatus && $timerStatus['student_id'] == $this->selectedStudent) {
                // Handle cancellation
                $timerService->handleExpiredTimer($this->selectedThesisTitle);
            }
        }

        $this->step--;

        if ($this->step == 2) {
            // Refresh thesis titles when going back to step 2
            $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
        }
    }
}