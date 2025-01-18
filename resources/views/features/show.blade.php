<x-app-layout>
<div class="container mx-auto py-6 space-y-8">
    <!-- feature Header -->
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold text-gray-800">{{ $feature['name'] }}</h1>
        @if (!empty($feature['description']))
            <p class="text-gray-600 mt-2">{{ $feature['description'] }}</p>
        @else
            <p class="text-gray-600 mt-2 italic">No description available for this feature.</p>
        @endif
    </div>

    <!-- Epics Grouped by Release -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Epics Grouped by Release</h2>
        @forelse ($epics as $release => $epicsInRelease)
            <div class="bg-white p-4 rounded shadow mb-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ $release }}</h3>
                <ul class="mt-2 space-y-2">
                    @foreach ($epicsInRelease as $epic)
                        <li class="p-3 bg-gray-50 rounded shadow">
                            <h4 class="font-bold text-gray-900">{{ $epic['fields']['summary'] }}</h4>
                            <p class="text-sm text-gray-600">Key: {{ $epic['key'] }}</p>
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
        <h2 class="text-2xl font-bold mb-4">Bugs</h2>
        @if (count($bugs))
            <div class="bg-red-100 p-4 rounded shadow">
                <ul class="space-y-2">
                    @foreach ($bugs as $bug)
                        <li class="p-3 bg-white rounded shadow">
                            <h4 class="font-bold text-red-800">{{ $bug['fields']['summary'] }}</h4>
                            <p class="text-sm text-gray-600">Key: {{ $bug['key'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-gray-600">No bugs found for this feature.</p>
        @endif
    </div>

    <!-- Requests -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Requests</h2>
        @if (count($requests))
            <div class="bg-blue-100 p-4 rounded shadow">
                <ul class="space-y-2">
                    @foreach ($requests as $request)
                        <li class="p-3 bg-white rounded shadow">
                            <h4 class="font-bold text-blue-800">{{ $request['fields']['summary'] }}</h4>
                            <p class="text-sm text-gray-600">Key: {{ $request['key'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-gray-600">No requests found for this feature.</p>
        @endif
    </div>
</div>
</x-app-layout>
