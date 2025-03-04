<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold">Live Activity Monitor</h2>
        <div class="text-sm text-gray-500">
            Real-time updates enabled
        </div>
    </div>

    <div class="space-y-4">
        @foreach($activities as $activity)
            <div class="p-4 bg-white rounded-lg shadow-sm border">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $activity->student ? $activity->student->name : 'Unknown' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ $activity->action }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        IP: {{ $activity->ip_address }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
