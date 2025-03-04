<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div
            class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl">
        </div>
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div class="text-center">
                    <h1 class="text-2xl font-semibold text-gray-900">Thesis Title Selection</h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Fill in your details and select a thesis title.
                    </p>
                </div>

                @if ($success)
                    <div class="mt-8 bg-green-50 border border-green-500 rounded-lg p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="mt-3 text-lg font-medium text-green-800">Selection Successful!</h2>
                        <p class="mt-2 text-sm text-green-600">
                            Your thesis title selection has been submitted successfully. A confirmation email has been sent
                            to your email address.
                        </p>
                        <div class="mt-6">
                            <h3 class="text-md font-medium text-gray-900">Your Selection Details:</h3>
                            <div class="mt-2 text-sm text-gray-700">
                                <p><span class="font-medium">Name:</span> {{ $student->name }}</p>
                                <p><span class="font-medium">NPM:</span> {{ $student->npm }}</p>
                                <p><span class="font-medium">Thesis Title:</span> {{ $selectedThesisTitle->title }}</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button wire:click="startOver"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Start Over
                            </button>
                        </div>
                    </div>
                @elseif ($confirmationStep)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Confirm Your Selection</h3>
                        <div class="mt-4 bg-gray-50 p-4 rounded-md">
                            <div class="text-sm text-gray-700">
                                <p><span class="font-medium">Name:</span> {{ $student->name }}</p>
                                <p><span class="font-medium">NPM:</span> {{ $student->npm }}</p>
                                <p><span class="font-medium">Class:</span> {{ $student->class }}</p>
                                <p><span class="font-medium">Thesis Topic:</span> {{ $student->thesis_topic }}</p>
                                <p><span class="font-medium">Email:</span> {{ $student->email }}</p>
                                <p class="mt-3"><span class="font-medium">Selected Thesis Title:</span></p>
                                <p class="mt-1 p-2 bg-white rounded border border-gray-200">
                                    {{ $selectedThesisTitle->title }}</p>
                                @if ($selectedThesisTitle->description)
                                    <p class="mt-2 p-2 bg-white rounded border border-gray-200 text-xs">
                                        {{ $selectedThesisTitle->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between space-x-4">
                            <button wire:click="$set('confirmationStep', false)"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Back
                            </button>
                            <button wire:click="confirmSelection"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Confirm Selection
                            </button>
                        </div>
                    </div>
                @else
                    <div class="mt-8">
                        <form wire:submit.prevent="selectThesisTitle">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="studentName" class="block text-sm font-medium text-gray-700">Name</label>
                                    <select wire:model="studentName" id="studentName"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select your name</option>
                                        @foreach (App\Models\Student::orderBy('name')->pluck('name') as $name)
                                            <option value="{{ $name }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('studentName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="npm" class="block text-sm font-medium text-gray-700">NPM</label>
                                    <input type="text" wire:model="npm" id="npm"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('npm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="token" class="block text-sm font-medium text-gray-700">Token</label>
                                    <input type="text" wire:model.debounce.500ms="token" id="token"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('token') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <p class="mt-1 text-xs text-gray-500">Enter the 5-character token sent to your email</p>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="email" id="email"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <p class="mt-1 text-xs text-gray-500">Must be a valid @ui.ac.id email</p>
                                </div>

                                @if ($student)
                                    <div>
                                        <div class="bg-gray-50 p-3 rounded-md mb-4">
                                            <p class="text-sm text-gray-700"><span class="font-medium">Class:</span>
                                                {{ $student->class }}</p>
                                            <p class="text-sm text-gray-700"><span class="font-medium">Thesis Topic:</span>
                                                {{ $student->thesis_topic }}</p>
                                        </div>

                                        <label for="selectedThesisId" class="block text-sm font-medium text-gray-700">Select
                                            Thesis Title</label>
                                        <select wire:model="selectedThesisId" id="selectedThesisId"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Select a thesis title</option>
                                            @foreach ($availableThesisTitles as $thesis)
                                                <option value="{{ $thesis->id }}">{{ $thesis->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedThesisId')
                                            <span class="text-red-500 text-xs">{{ $message ?? 'Error tidak terdefinisi' }}</span>
                                        @enderror
                                        @if (count($availableThesisTitles) === 0)
                                            <p class="mt-2 text-xs text-red-500">No available thesis titles for your topic. Please
                                                contact the administrator.</p>
                                        @endif
                                    </div>
                                @endif

                                <div class="pt-4">
                                    <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Continue
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- // Help Section --}}
                <div class="mt-10 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-500">
                        Need help? Contact the administrator
                    </p>
                    <div class="mt-3 flex justify-center">
                        <a href="https://wa.me/6285XXXXXXXX" target="_blank"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.016c.387.387 1.042.387 1.429 0 .387-.387.387-1.042 0-1.429l-6.944-6.944c-.387-.387-1.042-.387-1.429 0-.387.387-.387 1.042 0 1.429l6.944 6.944zm-12.944 0c.387.387 1.042.387 1.429 0 .387-.387.387-1.042 0-1.429l-6.944-6.944c-.387-.387-1.042-.387-1.429 0-.387.387-.387 1.042 0 1.429l6.944 6.944zm12.944 0" />
                            </svg>
                            WhatsApp Admin
                        </a>
                    </div>
                </div>
            </div>
