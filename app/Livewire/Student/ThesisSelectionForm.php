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
    public $search = '';
    public $statusFilter = 'all';
    public $sortField = 'status';
    public $sortDirection = 'asc';
    public $isSearching = false; // Flag to indicate search is in progress
    public $searchRelevanceActive = false; // Flag to indicate relevance-based sorting is active
    public $error = null;
    public $success = null;
    public $activeCountdown = null;
    public $activeCountdownId = null;
    protected $listeners = [
        'echo:thesis-selections,thesis.update' => 'handleThesisUpdate',
        'countdownEnded'                       => 'handleCountdownEnded',
        'searchRelevance'                      => 'applySearchRelevance',
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
        // Initialize students with default sorting
        $this->students = Student::orderBy('name')->get();

        // Initialize the component with thesis titles if a student is selected
        if ($this->selectedStudent) {
            $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
        }

        // Listen for real-time updates
        $this->setupPusherListeners();
    }

    protected function setupPusherListeners()
{
    // This is already handled in the $listeners property, but we'll enhance it
    // Make sure your ThesisSelectionEvent broadcasts properly
}

    public function render()
    {
        return view('livewire.student.thesis-selection-form')
            ->layout('layouts.guest');
    }

     protected function loadThesisTitles($topic)
    {
        $this->isSearching = true;

        // Start with the base query
        $query = ThesisTitle::where('topic', $topic)
            ->whereIn('status', ['Available', 'In Selection', 'Unavailable']);

        // Apply search if provided
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter === 'available') {
            $query->where('status', 'Available');
        } elseif ($this->statusFilter === 'in-selection') {
            $query->where('status', 'In Selection');
        } elseif ($this->statusFilter === 'unavailable') {
            $query->where('status', 'Unavailable');
        } else {
            // If 'all', show both available and in-selection
            $query->whereIn('status', ['Available', 'In Selection', 'Unavailable']);
        }

        // Apply sorting - if not using relevance-based sorting
        if (!$this->searchRelevanceActive || empty($this->search)) {
            if ($this->sortField === 'status') {
                $query->orderBy('status', $this->sortDirection);
            } elseif ($this->sortField === 'title') {
                $query->orderBy('title', $this->sortDirection);
            }
        }

        // Get the results
        $titles = $query->get();

        // Apply relevance-based sorting if search is not empty
        if (!empty($this->search) && $titles->count() > 0) {
            $this->searchRelevanceActive = true;
            $titles = $this->sortByRelevance($titles, $this->search);
        } else {
            $this->searchRelevanceActive = false;
        }

        $this->thesisTitles = $titles;
        $this->refreshThesisTitlesStatus();
        $this->isSearching = false;
    }
    protected function sortByRelevance($titles, $searchQuery)
    {
        // Lowercase the search query for case-insensitive matching
        $searchQuery = strtolower($searchQuery);
        $searchTerms = explode(' ', $searchQuery);

        // Filter out empty terms
        $searchTerms = array_filter($searchTerms, function ($term) {
            return !empty($term);
        });

        // Calculate relevance score for each thesis title
        $scoredTitles = $titles->map(function ($thesis) use ($searchTerms, $searchQuery) {
            $score = 0;
            $title = strtolower($thesis->title);
            $description = strtolower($thesis->description);

            // Exact match in title gets highest score
            if (str_contains($title, $searchQuery)) {
                $score += 100;
            }

            // Exact match in description
            if (str_contains($description, $searchQuery)) {
                $score += 50;
            }

            // Partial matches for each search term
            foreach ($searchTerms as $term) {
                // Match in title
                if (str_contains($title, $term)) {
                    $score += 20;

                    // Bonus for term at the beginning of title
                    if (strpos($title, $term) === 0) {
                        $score += 10;
                    }
                }

                // Match in description
                if (str_contains($description, $term)) {
                    $score += 10;
                }
            }

            // Give some priority to available titles
            if ($thesis->status === 'Available') {
                $score += 5;
            }

            $thesis->relevanceScore = $score;
            return $thesis;
        });

        // Sort by relevance score (descending)
        $sortedTitles = $scoredTitles->sortByDesc('relevanceScore');

        return $sortedTitles->values();
    }

    // Add these methods to handle search, filter and sort
    public function updatedSearch()
    {
        $this->isSearching = true;
        $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
    }

    public function updatedSortField()
    {
        // Reset relevance-based sorting when manually changing sort field
        $this->searchRelevanceActive = false;
        $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
    }

    public function updatedSortDirection()
    {
        // Reset relevance-based sorting when manually changing sort direction
        $this->searchRelevanceActive = false;
        $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
    }

    public function updatedStatusFilter()
    {
        $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
    }

    // Apply search relevance sorting
    public function applySearchRelevance()
    {
        if (!empty($this->search)) {
            $this->searchRelevanceActive = true;
            $this->thesisTitles = $this->sortByRelevance($this->thesisTitles, $this->search);
        }
    }

    protected function refreshThesisTitlesStatus()
    {
        $this->thesisTitlesStatus = [];
        $this->countdowns = [];

        foreach ($this->thesisTitles as $thesis) {
            // Get current database status (most accurate)
            $currentStatus = ThesisTitle::find($thesis->id)->status;

            // Check for active selection with timer
            $activeSelection = ThesisSelection::where('thesis_title_id', $thesis->id)
                ->where('status', 'Pending')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if ($activeSelection) {
                $this->thesisTitlesStatus[$thesis->id] = 'In Selection';
                $this->countdowns[$thesis->id] = $activeSelection->expires_at->timestamp;
            } else {
                // If there's no active selection but status is 'In Selection',
                // it might be stale data - refresh from database
                if ($currentStatus === 'Available') {
                    $this->thesisTitlesStatus[$thesis->id] = 'Available';
                } elseif ($currentStatus === 'Unavailable') {
                    $this->thesisTitlesStatus[$thesis->id] = 'Unavailable';
                } else {
                    // Check if any selections recently expired
                    $expiredSelection = ThesisSelection::where('thesis_title_id', $thesis->id)
                        ->where('status', 'Pending')
                        ->where('expires_at', '<=', now())
                        ->latest()
                        ->first();

                    if ($expiredSelection) {
                        // Mark as expired and update status
                        $expiredSelection->status = 'Expired';
                        $expiredSelection->save();

                        // Update thesis title status
                        $thesis->status = 'Available';
                        $thesis->save();

                        $this->thesisTitlesStatus[$thesis->id] = 'Available';

                        // Broadcast this change
                        event(new ThesisSelectionEvent($thesis, 'available'));
                    } else {
                        // Default to database state
                        $this->thesisTitlesStatus[$thesis->id] = $currentStatus;
                    }
                }
            }
        }
    }

    public function handleThesisUpdate($event)
    {
        // Update status judul skripsi berdasarkan event real-time
        if (isset($event['id'])) {
            $thesisId = $event['id'];

            // Get the thesis title from database to ensure we have latest data
            $thesis = ThesisTitle::find($thesisId);
            if (!$thesis)
                return;

            if ($event['action'] == 'selected') {
                $this->thesisTitlesStatus[$thesisId] = 'In Selection';
                $expiresAt = Carbon::createFromTimestamp($event['expiresAt']);
                $this->countdowns[$thesisId] = $event['expiresAt'];

                // If user has this thesis selected, but someone else has claimed it
                if ($this->selectedThesisTitle == $thesisId && $event['student_id'] != $this->selectedStudent) {
                    $this->selectedThesisTitle = null;
                    $this->dispatch('notify', ['type' => 'warning', 'message' => 'Judul skripsi yang Anda pilih telah dipilih oleh mahasiswa lain.']);
                }

                // If we're in confirmation step and our selection is no longer valid
                if ($this->step == 3 && $this->selectedThesisTitle == $thesisId && $event['student_id'] != $this->selectedStudent) {
                    $this->error = 'Judul skripsi yang Anda pilih telah dipilih oleh mahasiswa lain.';
                    $this->goBack();
                    $this->dispatch('notify', ['type' => 'error', 'message' => 'Waktu pemilihan judul skripsi telah habis atau telah dipilih oleh mahasiswa lain.']);
                }

            } elseif ($event['action'] == 'expired' || $event['action'] == 'available') {
                $this->thesisTitlesStatus[$thesisId] = 'Available';
                unset($this->countdowns[$thesisId]);

                // If we're in step 3 and our timer expired
                if ($this->step == 3 && $this->selectedThesisTitle == $thesisId) {
                    $this->error = 'Waktu pemilihan judul skripsi telah habis. Silakan pilih judul lain.';
                    $this->goBack();
                    $this->dispatch('notify', ['type' => 'error', 'message' => 'Waktu pemilihan judul skripsi telah habis. Silakan pilih judul lain.']);
                }

            } elseif ($event['action'] == 'unavailable') {
                $this->thesisTitlesStatus[$thesisId] = 'Unavailable';
                unset($this->countdowns[$thesisId]);

                // If user has this thesis selected, but it's now unavailable
                if ($this->selectedThesisTitle == $thesisId) {
                    $this->selectedThesisTitle = null;
                    $this->dispatch('notify', ['type' => 'warning', 'message' => 'Judul skripsi yang Anda pilih telah dipilih oleh mahasiswa lain.']);
                }

                // If we're in confirmation step and our selection is no longer valid
                if ($this->step == 3 && $this->selectedThesisTitle == $thesisId) {
                    $this->error = 'Judul skripsi yang Anda pilih telah dipilih oleh mahasiswa lain.';
                    $this->goBack();
                    $this->dispatch('notify', ['type' => 'error', 'message' => 'Judul skripsi telah dipilih oleh mahasiswa lain.']);
                }
            }

            // Refresh frontend
            $this->dispatch('refreshCountdowns');
        }
    }

    public function handleCountdownEnded($thesisId)
    {
        // Ketika timer habis, update status di database dan broadcast
        try {
            DB::beginTransaction();

            $thesis = ThesisTitle::find($thesisId);
            $activeSelection = ThesisSelection::where('thesis_title_id', $thesisId)
                ->where('status', 'Pending')
                ->latest()
                ->first();

            if ($thesis) {
                // Verifikasi jika benar-benar sudah kedaluwarsa dengan membandingkan timestamp
                if ($activeSelection && $activeSelection->expires_at <= now()) {
                    // Update status judul menjadi Available
                    $thesis->status = 'Available';
                    $thesis->save();

                    // Update selection status
                    $activeSelection->status = 'Expired';
                    $activeSelection->save();

                    // Broadcast ke seluruh user
                    event(new ThesisSelectionEvent($thesis, 'expired'));

                    // If we're in step 3 and this is our thesis, go back to step 2
                    if ($this->step == 3 && $this->selectedThesisTitle == $thesisId) {
                        $this->error = 'Waktu pemilihan judul skripsi telah habis. Silakan pilih judul lain.';
                        $this->goBack();
                        $this->dispatch('notify', ['type' => 'error', 'message' => 'Waktu pemilihan judul skripsi telah habis. Silakan pilih judul lain.']);
                    }
                }

                // Update local state - lakukan ini terlepas dari apakah ada active selection atau tidak
                $this->thesisTitlesStatus[$thesisId] = $thesis->status; // Gunakan status dari database
                unset($this->countdowns[$thesisId]);
                $this->dispatch('refreshCountdowns');
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

    // Get fresh status from database
    $thesis = ThesisTitle::find($thesisId);
    if (!$thesis) {
        $this->error = 'Judul skripsi tidak ditemukan.';
        return;
    }

    // Check real-time status
    $activeSelection = ThesisSelection::where('thesis_title_id', $thesisId)
        ->where('status', 'Pending')
        ->where('expires_at', '>', now())
        ->first();

    // Only allow selection if thesis is truly Available
    if ($thesis->status !== 'Available' || $activeSelection) {
        $this->error = 'Judul skripsi ini sedang dipilih oleh mahasiswa lain atau tidak tersedia.';
        $this->loadThesisTitles(Student::find($this->selectedStudent)->thesis_topic);
        return;
    }

    // Mulai timer untuk pemilihan ini
    try {
        DB::beginTransaction();

        // Update thesis status
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
        event(new ThesisSelectionEvent($thesis, 'selected', $expiresAt->timestamp, $this->selectedStudent));

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

    protected function filterThesisTitles()
    {
        if (empty($this->searchQuery)) {
            $this->filteredThesisTitles = $this->thesisTitles;
        } else {
            $this->filteredThesisTitles = $this->thesisTitles->filter(function ($thesis) {
                return str_contains(strtolower($thesis->title), strtolower($this->searchQuery)) ||
                    str_contains(strtolower($thesis->description), strtolower($this->searchQuery));
            });
        }
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

    public function confirmSelection()
    {
        $student = Student::find($this->selectedStudent);
        $thesisTitle = ThesisTitle::find($this->selectedThesisTitle);

        if (!$student || !$thesisTitle) {
            $this->error = 'Data tidak valid.';
            return;
        }

        try {
            // Start database transaction
            DB::beginTransaction();

            // Check if timer is still valid
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

            // Update thesis status to Unavailable
            $thesisTitle->status = 'Unavailable';
            $thesisTitle->save();

            // Update selection status to Approved
            $activeSelection->status = 'Approved';
            $activeSelection->save();

            // Update student status
            $student->has_selected = true;
            $student->save();

            // Log activity
            $student->activityLogs()->create([
                'action'      => 'thesis_selection',
                'description' => 'Mahasiswa memilih judul skripsi: ' . $thesisTitle->title,
                'ip_address'  => request()->ip()
            ]);

            // Broadcast to admin and all users that this title is now unavailable
            event(new ThesisSelectionEvent($thesisTitle, 'unavailable'));

            // Clear active countdown
            $this->activeCountdown = null;
            $this->activeCountdownId = null;

            DB::commit();

            $this->step = 4;
            $this->success = 'Pemilihan judul skripsi berhasil. Silakan cek email Anda untuk konfirmasi.';
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function debugConfirmation()
    {
        $student = Student::find($this->selectedStudent);
        $thesisTitle = ThesisTitle::find($this->selectedThesisTitle);

        $activeSelection = null;
        if ($student && $thesisTitle) {
            $activeSelection = ThesisSelection::where('thesis_title_id', $thesisTitle->id)
                ->where('student_id', $student->id)
                ->where('status', 'Pending')
                ->latest()
                ->first();
        }

        $debugInfo = [
            'student_found'    => $student ? true : false,
            'thesis_found'     => $thesisTitle ? true : false,
            'active_selection' => $activeSelection ? [
                'id'      => $activeSelection->id,
                'status'  => $activeSelection->status,
                'expired' => $activeSelection->expires_at < now()
            ] : null,
            'current_step'     => $this->step
        ];

        $this->error = 'Debug info: ' . json_encode($debugInfo);
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
