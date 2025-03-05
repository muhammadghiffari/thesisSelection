<div>
    @if ($errorMessage)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error! </strong>
            <span class="block sm:inline">{!! $errorMessage !!}</span>
        </div>
    @endif
    
    @if ($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sukses! </strong>
            <span class="block sm:inline">{{ $successMessage }}</span>
        </div>
    @endif
    
    <div x-data="{ step: @entangle('step') }">
        <!-- Step 1: Student Information -->
        <div x-show="step === 1" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Mahasiswa</label>
                    <select 
                        wire:model="selectedStudentId" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    >
                        <option value="">-- Pilih Nama --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelas</label>
                    <input 
                        type="text" 
                        value="{{ $studentClass }}" 
                        readonly 
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                    >
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Token</label>
                    <input 
                        type="text" 
                        wire:model="token" 
                        placeholder="Masukkan Token 5 Karakter" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">NPM</label>
                    <input 
                        type="text" 
                        wire:model="studentNpm" 
                        placeholder="Masukkan NPM 10 Digit" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                    >
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Email UI</label>
                <input 
                    type="email" 
                    wire:model="studentEmail" 
                    placeholder="Masukkan Email @ui.ac.id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                >
            </div>
            
            <div class="flex justify-end mt-4">
                <button 
                    wire:click="goToStep(2)" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Lanjutkan
                </button>
            </div>
        </div>
        
        <!-- Step 2: Thesis Selection -->
        <div x-show="step === 2" class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Pilih Judul Skripsi</h2>
            <p class="text-gray-600 mb-4">Topik: {{ $studentTopic }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($availableTheses as $thesis)
                    <div 
                        wire:click="selectThesis({{ $thesis['id'] }})" 
                        class="border rounded-lg p-4 cursor-pointer transition-all 
                            {{ $selectedThesisId == $thesis['id'] ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : 'hover:border-blue-300' }}
                            {{ $thesis['disabled'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                        @if($thesis['disabled']) disabled @endif
                    >
                        <h3 class="font-semibold text-gray-800 mb-2">
                            {{ $thesis['title'] }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-2">
                            {{ Str::limit($thesis['description'], 100) }}
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium 
                                {{ $thesis['status'] == 'Available' ? 'text-green-600' : 
                                   ($thesis['status'] == 'In Selection' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $thesis['status'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500">
                        Tidak ada judul skripsi tersedia untuk topik Anda.
                    </div>
                @endforelse
            </div>
            
            <div class="flex justify-between mt-4">
                <button 
                    wire:click="goToStep(1)" 
                    class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    Kembali
                </button>
                <button 
                    wire:click="goToStep(3)" 
                    @if(!$selectedThesisId) disabled @endif
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                        {{ !$selectedThesisId ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    Konfirmasi Pilihan
                </button>
            </div>
        </div>
        
        <!-- Step 3: Confirmation -->
        <div x-show="step === 3" class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Konfirmasi Pilihan Skripsi</h2>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Mahasiswa</h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nama</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $studentName }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Kelas</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $studentClass }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Topik</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ $studentTopic }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-4">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Judul Skripsi Terpilih</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                    @if($selectedThesisId)
                        @php
                            $selectedThesis = collect($availableTheses)->firstWhere('id', $selectedThesisId);
                        @endphp
                        <h4 class="text-lg font-semibold text-gray-800">{{ $selectedThesis['title'] }}</h4>
                        <p class="text-gray-600 mt-2">{{ $selectedThesis['description'] }}</p>
                    @endif
                </div>
            </div>
            
            <div class="flex justify-between mt-4">
                <button 
                    wire:click="goToStep(2)" 
                    class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    Kembali
                </button>
                <button 
                    wire:click="submitSelection"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                        {{ $loading ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    <span wire:loading.remove>Konfirmasi & Kirim</span>
                    <span wire:loading>Sedang Mengirim...</span>
                </button>
            </div>
        </div>
    </div>
</div>