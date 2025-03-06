<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div class="text-2xl font-bold text-center mb-6">
                Sistem Pemilihan Judul Skripsi
            </div>
            
            @if ($error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ $error }}</span>
            </div>
            @endif
            
            @if ($success)
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ $success }}</span>
            </div>
            @endif
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $step * 25 }}%"></div>
            </div>
            
            <div class="flex justify-between mb-6 text-sm">
                <div class="text-center {{ $step >= 1 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 1 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">1</div>
                    Data Diri
                </div>
                <div class="text-center {{ $step >= 2 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 2 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">2</div>
                    Judul Skripsi
                </div>
                <div class="text-center {{ $step >= 3 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 3 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">3</div>
                    Konfirmasi
                </div>
                <div class="text-center {{ $step >= 4 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 4 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">4</div>
                    Selesai
                </div>
            </div>
            
            <!-- Step 1: Data Diri -->
            @if ($step == 1)
            <div class="space-y-4">
                <div>
                    <label for="selectedStudent" class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                    <div class="mt-1 relative">
                        <select id="selectedStudent" wire:model.live="selectedStudent" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Pilih Nama Mahasiswa</option>
                            @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('selectedStudent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <!-- Show Class and Topic if student is selected -->
                @if ($selectedStudent)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kelas</label>
                        <div class="mt-1 p-2 bg-gray-100 rounded-md">
                            {{ $studentClass ?: 'Sedang memuat...' }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Topik Skripsi</label>
                        <div class="mt-1 p-2 bg-gray-100 rounded-md">
                            {{ $studentTopic ?: 'Sedang memuat...' }}
                        </div>
                    </div>
                </div>
                @endif
                
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700">Token</label>
                    <div class="mt-1">
                        <input type="text" id="token" wire:model.live="token" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Masukkan token 5 karakter" maxlength="5">
                    </div>
                    @error('token') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="npm" class="block text-sm font-medium text-gray-700">NPM</label>
                    <div class="mt-1">
                        <input type="text" id="npm" wire:model.live="npm" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Masukkan NPM 10 digit" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    @error('npm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email @ui.ac.id</label>
                    <div class="mt-1">
                        <input type="email" id="email" wire:model.live="email" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="nama@ui.ac.id">
                    </div>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-end pt-5">
                    <button type="button" wire:click="continueToStep2" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Lanjutkan
                    </button>
                </div>
            </div>
            @endif
            
              <!-- Step 2: Judul Skripsi -->
            @if ($step == 2)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Judul Skripsi</h3>
                
                <div class="space-y-4 mb-6">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm font-medium text-gray-700">Mahasiswa: <span class="font-semibold">{{ optional(App\Models\Student::find($selectedStudent))->name }}</span></p>
                        <p class="text-sm font-medium text-gray-700">Kelas: <span class="font-semibold">{{ $studentClass }}</span></p>
                        <p class="text-sm font-medium text-gray-700">Topik: <span class="font-semibold">{{ $studentTopic }}</span></p>
                    </div>
                </div>
                
                <div class="space-y-4" id="thesis-titles-container">
                    @foreach ($thesisTitles as $thesis)
                    <div 
                        class="relative flex items-start border rounded-md p-4 hover:bg-gray-50 {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'In Selection' ? 'bg-yellow-50 border-yellow-300' : '' }}"
                        id="thesis-container-{{ $thesis->id }}"
                    >
                        <div class="flex items-center h-5">
                            <input 
                                id="thesis-{{ $thesis->id }}" 
                                wire:model.live="selectedThesisTitle" 
                                value="{{ $thesis->id }}" 
                                type="radio" 
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'In Selection' ? 'disabled' : '' }}
                            >
                        </div>
                        <div class="ml-3 flex-1">
                            <label for="thesis-{{ $thesis->id }}" class="font-medium text-gray-700">{{ $thesis->title }}</label>
                            <p class="text-gray-500 text-sm">{{ $thesis->description }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'In Selection' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}"
                                    id="thesis-status-{{ $thesis->id }}"
                                >
                                    {{ isset($thesisTitlesStatus[$thesis->id]) ? $thesisTitlesStatus[$thesis->id] : 'Available' }}
                                </span>
                                
                                <!-- Countdown Timer (only shown for 'In Selection' status) -->
                                @if(isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'In Selection')
                                <span 
                                    class="countdown-timer inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                    data-thesis-id="{{ $thesis->id }}"
                                    data-expires-at="{{ $countdowns[$thesis->id] ?? 0 }}"
                                >
                                    Waktu tersisa: <span id="countdown-{{ $thesis->id }}">01:00</span>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    @error('selectedThesisTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-between pt-5">
                    <button type="button" wire:click="goBack" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali
                    </button>
                    <button type="button" wire:click="continueToStep3" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Lanjutkan
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Step 3: Konfirmasi -->
            @if ($step == 3)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Pemilihan Judul Skripsi</h3>
                
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <div class="mb-4">
                        <h4 class="text-base font-medium text-gray-800">Data Mahasiswa</h4>
                        <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ optional(App\Models\Student::find($selectedStudent))->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">NPM</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $npm }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $studentClass }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $email }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Topik Skripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $studentTopic }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h4 class="text-base font-medium text-gray-800">Judul Skripsi Terpilih</h4>
                        <div class="mt-2 bg-white p-4 rounded-md border border-gray-200">
                            <h5 class="text-sm font-bold text-gray-900">{{ optional(App\Models\ThesisTitle::find($selectedThesisTitle))->title }}</h5>
                            <p class="mt-1 text-sm text-gray-600">{{ optional(App\Models\ThesisTitle::find($selectedThesisTitle))->description }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Pemilihan judul skripsi tidak dapat diubah setelah konfirmasi. Pastikan data yang Anda masukkan sudah benar.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between pt-5">
                    <button type="button" wire:click="goBack" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali
                    </button>
                    <button type="button" wire:click="confirmSelection" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Konfirmasi Pemilihan
                    </button>
                </div>
            </div>
            @endif
            
            <!-- Step 4: Selesai -->
            @if ($step == 4)
            <div class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-xl font-medium text-gray-900">Pemilihan Judul Skripsi Berhasil!</h3>
                <div class="mt-2 text-sm text-gray-500">
                    <p>Silakan cek email Anda untuk informasi selanjutnya. Email konfirmasi akan dikirim ke {{ $email }}.</p>
                </div>
                <div class="mt-6">
                    <button type="button" wire:click="resetForm" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali ke Awal
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // JavaScript untuk menangani countdown timer
    document.addEventListener('livewire:initialized', () => {
        initializeCountdowns();

        @this.on('refreshCountdowns', () => {
            // Re-initialize countdowns when data is refreshed
            initializeCountdowns();
        });
    });

    function initializeCountdowns() {
        // Clear any existing intervals
        document.querySelectorAll('.countdown-timer').forEach(timer => {
            const thesisId = timer.getAttribute('data-thesis-id');
            if (window['countdownInterval_' + thesisId]) {
                clearInterval(window['countdownInterval_' + thesisId]);
            }

            const expiresAt = parseInt(timer.getAttribute('data-expires-at'));
            if (expiresAt) {
                startCountdown(thesisId, expiresAt);
            }
        });
    }