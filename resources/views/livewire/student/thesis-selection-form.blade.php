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
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ $success }}</span>
                </div>
            @endif

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $step * 25 }}%"></div>
            </div>

            <div class="flex justify-between mb-6 text-sm">
                <div class="text-center {{ $step >= 1 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div
                        class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 1 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">
                        1</div>
                    Data Diri
                </div>
                <div class="text-center {{ $step >= 2 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div
                        class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 2 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">
                        2</div>
                    Judul Skripsi
                </div>
                <div class="text-center {{ $step >= 3 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div
                        class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 3 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">
                        3</div>
                    Konfirmasi
                </div>
                <div class="text-center {{ $step >= 4 ? 'text-blue-600 font-medium' : 'text-gray-500' }}">
                    <div
                        class="w-8 h-8 mx-auto flex items-center justify-center rounded-full {{ $step >= 4 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-500' }} mb-1">
                        4</div>
                    Selesai
                </div>
            </div>

            <!-- Step 1: Data Diri -->
            @if ($step == 1)
                <div class="space-y-4">
                    <div>
                        <label for="selectedStudent" class="block text-sm font-medium text-gray-700">Nama
                            Mahasiswa</label>
                        <div class="mt-1 relative">
                            <select id="selectedStudent" wire:model.live="selectedStudent"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Pilih Nama Mahasiswa</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('selectedStudent')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
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
                            <input type="text" id="token" wire:model.live="token"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Masukkan token 5 karakter" maxlength="5">
                        </div>
                        @error('token')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="npm" class="block text-sm font-medium text-gray-700">NPM</label>
                        <div class="mt-1">
                            <input type="text" id="npm" wire:model.live="npm"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Masukkan NPM 10 digit" maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        @error('npm')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email @ui.ac.id</label>
                        <div class="mt-1">
                            <input type="email" id="email" wire:model.live="email"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="nama@ui.ac.id">
                        </div>
                        @error('email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-5">
                        <button type="button" wire:click="continueToStep2"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Lanjutkan
                        </button>
                    </div>
                </div>
            @endif

            <!-- Step 2: Judul Skripsi -->
            @if ($step == 2)
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Judul Skripsi</h3>

                    <!-- Search, Filter, and Sort Controls -->
                    <div class="space-y-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm font-medium text-gray-700">Mahasiswa: <span
                                    class="font-semibold">{{ optional(App\Models\Student::find($selectedStudent))->name }}</span>
                            </p>
                            <p class="text-sm font-medium text-gray-700">Kelas: <span
                                    class="font-semibold">{{ $studentClass }}</span></p>
                            <p class="text-sm font-medium text-gray-700">Topik: <span
                                    class="font-semibold">{{ $studentTopic }}</span></p>
                        </div>

                        <!-- Search and filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Judul
                                    Skripsi</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="text" id="search" wire:model.debounce.300ms="search"
                                        class="block w-full pr-10 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Masukkan kata kunci atau kode">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Add this after the search filters section -->
                            @if ($searchRelevanceActive)
                                <div class="mt-2 bg-blue-50 border-l-4 border-blue-400 p-2 text-sm">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-blue-700">
                                                Hasil diurutkan berdasarkan relevansi dengan kata kunci
                                                "{{ $search }}"
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- Status Filter -->
                            <div>
                                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Filter
                                    Status</label>
                                <select id="statusFilter" wire:model.live="statusFilter"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="all">Semua</option>
                                    <option value="available">Tersedia</option>
                                    <option value="in-selection">Sedang Dipilih</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label for="sortField" class="block text-sm font-medium text-gray-700">Urutkan
                                    Berdasarkan</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <select id="sortField" wire:model.live="sortField"
                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                                        {{ $searchRelevanceActive ? 'disabled' : '' }}>
                                        <option value="status">Status (Default)</option>
                                        <option value="title">Judul</option>
                                    </select>
                                    <button
                                        wire:click="$set('sortDirection', '{{ $sortDirection === 'asc' ? 'desc' : 'asc' }}')"
                                        class="ml-2 p-2 bg-gray-100 rounded hover:bg-gray-200 {{ $searchRelevanceActive ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $searchRelevanceActive ? 'disabled' : '' }}>
                                        @if ($sortDirection === 'asc')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- This code should be added to the existing Step 2 section where the thesis titles are displayed -->

                    </div>
                </div>
                @if ($isSearching)
                    <div class="flex justify-center items-center py-4">
                        <svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="ml-2 text-sm text-gray-700">Mencari...</span>
                    </div>
                @endif
                <!-- Thesis list -->
                <div class="space-y-4" id="thesis-titles-container">
                    @if ($thesisTitles->isEmpty())
                        <div class="text-center py-6 bg-gray-50 rounded-md">
                            <p class="text-gray-500">Tidak ada judul skripsi yang sesuai dengan kriteria pencarian.
                            </p>
                        </div>
                    @else
                        @foreach ($thesisTitles as $thesis)
                            <!-- Thesis item -->
                            <div class="relative flex items-start border rounded-md p-4 hover:bg-gray-50
                                                                    {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'In Selection' ? 'bg-yellow-50 border-yellow-300' : '' }}
                                                                    {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'Unavailable' ? 'bg-gray-100 border-gray-300' : '' }}"
                                id="thesis-container-{{ $thesis->id }}">
                                <div class="flex items-center h-5">
                                    <input id="thesis-{{ $thesis->id }}" wire:model.live="selectedThesisTitle"
                                        value="{{ $thesis->id }}" type="radio"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                        {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] != 'Available' ? 'disabled' : '' }}>
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="thesis-{{ $thesis->id }}"
                                        class="font-medium text-gray-700 {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'Unavailable' ? 'line-through text-gray-500' : '' }}">{{ $thesis->title }}</label>
                                    <p class="text-gray-500 text-sm">{{ $thesis->description }}</p>
                                    <div class="flex justify-between items-center mt-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                                {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'In Selection' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                                {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'Available' ? 'bg-green-100 text-green-800' : '' }}
                                                                                {{ isset($thesisTitlesStatus[$thesis->id]) && $thesisTitlesStatus[$thesis->id] == 'Unavailable' ? 'bg-red-100 text-red-800' : '' }}"
                                            id="thesis-status-{{ $thesis->id }}">
                                            @if (isset($thesisTitlesStatus[$thesis->id]))
                                                @if ($thesisTitlesStatus[$thesis->id] == 'Available')
                                                    Tersedia
                                                @elseif($thesisTitlesStatus[$thesis->id] == 'In Selection')
                                                    Sedang Dipilih
                                                @elseif($thesisTitlesStatus[$thesis->id] == 'Unavailable')
                                                    Sudah Dipilih
                                                @endif
                                            @else
                                                Tersedia
                                            @endif
                                        </span>
                                        <!-- Kode lain yang diperlukan di sini -->
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
        </div>
        <div class="flex justify-between pt-5">
            <button type="button" wire:click="goBack"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </button>
            <button type="button" wire:click="continueToStep3"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Lanjutkan
            </button>
        </div>
    </div>
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
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ optional(App\Models\Student::find($selectedStudent))->name }}</dd>
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
                    <h5 class="text-sm font-bold text-gray-900">
                        {{ optional(App\Models\ThesisTitle::find($selectedThesisTitle))->title }}</h5>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ optional(App\Models\ThesisTitle::find($selectedThesisTitle))->description }}</p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Pemilihan judul skripsi tidak dapat diubah setelah konfirmasi. Pastikan data yang
                        Anda masukkan sudah benar.
                    </p>
                </div>
            </div>
        </div>
        <!-- Floating Timer -->
        @if (isset($countdowns[$selectedThesisTitle]))
            <div class="fixed bottom-4 right-4 bg-white px-4 py-3 shadow-lg rounded-lg border border-yellow-300">
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-gray-700">Waktu tersisa untuk konfirmasi:</span>
                    <span class="font-bold text-yellow-600 countdown-timer"
                        data-thesis-id="{{ $selectedThesisTitle }}"
                        data-expires-at="{{ $countdowns[$selectedThesisTitle] }}">
                        <span id="countdown-{{ $selectedThesisTitle }}">00:00</span>
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Harap konfirmasi sebelum waktu habis</p>
            </div>
        @endif

        <div class="flex justify-between pt-5">
            <button type="button" wire:click="goBack"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </button>
            <!-- Replace your existing confirmation button with this -->
            <button type="button" wire:click="confirmSelection" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <span wire:loading.remove wire:target="confirmSelection">
                    Konfirmasi Pemilihan
                </span>
                <span wire:loading wire:target="confirmSelection">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Processing...
                </span>
            </button>
            <!-- Add this button next to your confirmation button for testing -->
            <button type="button" wire:click="debugConfirmation"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Debug Info
            </button>
        </div>
    </div>
@endif

<!-- Step 4: Selesai -->
@if ($step == 4)
    <div class="text-center py-10">
        <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mt-2 text-xl font-medium text-gray-900">Pemilihan Judul Skripsi Berhasil!</h3>
        <div class="mt-2 text-sm text-gray-500">
            <p>Silakan cek email Anda untuk informasi selanjutnya. Email konfirmasi akan dikirim ke
                {{ $email }}.</p>
        </div>
        <div class="mt-6">
            <button type="button" wire:click="resetForm"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali ke Awal
            </button>
        </div>
    </div>
@endif
</div>
</div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Initialize the countdowns when the page loads
        initializeCountdowns();

        // Listen for Livewire events
        Livewire.on('refreshCountdowns', () => {
            console.log('Refreshing countdowns');
            initializeCountdowns();
        });

        // Create a notification system using Alpine.js
        window.addEventListener('notify', event => {
            const {
                type,
                message
            } = event.detail;

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
            'bg-green-100 text-green-800 border border-green-200'
        }`;

            notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'error' ?
                        '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                        type === 'warning' ?
                        '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>' :
                        '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
            </div>
        `;

            document.body.appendChild(notification);

            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        });

        // FITUR BARU DARI FILE 1: Handle search input events
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // We're using debounce in the Livewire component,
                // but we can also highlight the search terms in the results
                highlightSearchTerms(this.value);
            });
        }

        // FITUR BARU DARI FILE 1: Listen for search events
        document.addEventListener('searchCompleted', function() {
            // Trigger relevance-based sorting
            Livewire.dispatch('searchRelevance');
        });

        // Listen for Echo events
        window.Echo.channel('thesis-selections')
            .listen('.thesis.update', (event) => {
                console.log('Echo event received:', event);
                // The Livewire component will handle this via the listener
            });
    });

    // FITUR BARU DARI FILE 1: Function to highlight search terms in the results
    function highlightSearchTerms(searchQuery) {
        if (!searchQuery) return;

        const terms = searchQuery.toLowerCase().split(' ').filter(term => term.length > 0);
        if (terms.length === 0) return;

        const thesisElements = document.querySelectorAll('#thesis-titles-container label, #thesis-titles-container p');

        thesisElements.forEach(element => {
            const originalText = element.getAttribute('data-original-text') || element.textContent;

            // Store original text if not already stored
            if (!element.getAttribute('data-original-text')) {
                element.setAttribute('data-original-text', originalText);
            }

            let highlightedText = originalText;

            terms.forEach(term => {
                const regex = new RegExp(`(${term})`, 'gi');
                highlightedText = highlightedText.replace(regex, '<span class="bg-yellow-200">$1</span>');
            });

            // Only update if there are changes
            if (highlightedText !== originalText) {
                element.innerHTML = highlightedText;
            }
        });
    }

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

    function startCountdown(thesisId, expiresAt) {
        const countdownElement = document.getElementById('countdown-' + thesisId);
        if (!countdownElement) return;

        // Use server time for accuracy
        function getServerTimeOffset() {
            // This is approximate, ideally you'd have an endpoint
            // to get the exact server time
            return 0; // For simplicity, assuming no offset
        }

        function updateCountdown() {
            const now = Math.floor(Date.now() / 1000) + getServerTimeOffset();
            const timeLeft = expiresAt - now;

            if (timeLeft <= 0) {
                clearInterval(window['countdownInterval_' + thesisId]);
                countdownElement.textContent = '00:00';

                // Emit event to Livewire component that countdown has ended
                Livewire.dispatch('countdownEnded', thesisId);
                return;
            }

            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent =
                (minutes < 10 ? '0' + minutes : minutes) + ':' +
                (seconds < 10 ? '0' + seconds : seconds);
        }

        // Update immediately then set interval
        updateCountdown();
        window['countdownInterval_' + thesisId] = setInterval(updateCountdown, 1000);
    }
</script>