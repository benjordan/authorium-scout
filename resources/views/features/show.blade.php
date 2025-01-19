<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Feature Header -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $feature['name'] }}</h1>
                    @if (!empty($feature['description']))
                        <p class="text-gray-600 mt-2">{{ $feature['description'] }}</p>
                    @else
                        <p class="text-gray-600 mt-2 italic">No description available for this feature.</p>
                    @endif
                </div>
                <div class="space-x-4">
                    <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        Epics: {{ $counts['epics'] ?? 0 }}
                    </span>
                    <span class="bg-red-200 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                        Bugs: {{ $counts['bugs'] ?? 0 }}
                    </span>
                    <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        Requests: {{ $counts['requests'] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Epics Grouped by Release -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Epics Grouped by Release</h2>
            @forelse ($epics as $release => $epicsInRelease)
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">{{ $release }}</h3>
                    <ul class="mt-3 space-y-3">
                        @foreach ($epicsInRelease as $epic)
                            <li class="bg-gray-50 p-4 rounded shadow">
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('epics.show', $epic['key'] ?? '') }}" class="font-bold text-blue-600">
                                        {{ $epic['fields']['summary'] ?? 'No summary available' }}
                                    </a>
                                    <a href="https://cityinnovate.atlassian.net/browse/{{ $epic['key'] }}"
                                       class="text-xs text-gray-600 underline" target="_blank">
                                        View on Jira
                                    </a>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Key: {{ $epic['key'] ?? 'No key available' }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-gray-600">No epics found for this feature.</p>
            @endforelse
        </div>

        <!-- Bugs -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Bugs</h2>
            @if (!empty($bugs))
                <ul class="space-y-3">
                    @foreach ($bugs as $bug)
                        <li class="bg-red-50 p-4 rounded shadow">
                            <div class="flex justify-between items-center">
                                <p class="font-bold text-red-800">
                                    {{ $bug['fields']['summary'] ?? 'No summary available' }}
                                </p>
                                <a href="https://cityinnovate.atlassian.net/browse/{{ $bug['key'] }}"
                                   class="text-xs text-gray-600 underline" target="_blank">
                                    View on Jira
                                </a>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Key: {{ $bug['key'] ?? 'No key available' }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">No bugs found for this feature.</p>
            @endif
        </div>

        <!-- Requests -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Requests</h2>
            @if (!empty($requests))
                <ul class="space-y-3">
                    @foreach ($requests as $request)
                        <li class="bg-blue-50 p-4 rounded shadow">
                            <div class="flex justify-between items-center">
                                <p class="font-bold text-blue-800">
                                    {{ $request['fields']['summary'] ?? 'No summary available' }}
                                </p>
                                <a href="https://cityinnovate.atlassian.net/browse/{{ $request['key'] }}"
                                   class="text-xs text-gray-600 underline" target="_blank">
                                    View on Jira
                                </a>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Key: {{ $request['key'] ?? 'No key available' }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">No requests found for this feature.</p>
            @endif
        </div>
    </div>
</x-app-layout>
