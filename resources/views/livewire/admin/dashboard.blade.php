<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-semibold">Dashboard Admin</h2>
        <div class="text-sm text-gray-500">
            Real-time monitoring enabled
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Selections -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Selections</h3>
            <div class="space-y-4">
                @forelse($selections as $selection)
                    <div class="p-3 bg-gray-50 rounded-md">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $selection->student->name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ $selection->thesisTitle->title }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $selection->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                Status: {{ $selection->status }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">
                        No recent selections
                    </p>
                @endforelse
            </div>
        </div>

        <!-- Live Activities -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Live Activities</h3>
            <div class="space-y-4">
                @forelse($activities as $activity)
                    <div class="p-3 bg-gray-50 rounded-md">
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
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">
                        No recent activities
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
