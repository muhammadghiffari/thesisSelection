<div class="h-full flex flex-col">
    <!-- Chat Header -->
    <div class="bg-white px-4 py-3 border-b border-gray-200 shadow-sm">
        @if ($student)
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100">
                    <span class="text-sm font-medium leading-none text-indigo-700">
                        {{ substr($student->name, 0, 2) }}
                    </span>
                </span>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-gray-900">{{ $student->name }}</h3>
                <p class="text-xs text-gray-500">{{ $student->npm }} • {{ $student->class }}</p>
            </div>
        </div>
        @endif