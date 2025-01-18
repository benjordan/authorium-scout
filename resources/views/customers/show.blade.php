<!-- resources/views/customers/show.blade.php -->

<x-app-layout>
<div class="container mx-auto py-6 space-y-8">
    <!-- Customer Header -->
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold text-gray-800">{{ $customer['name'] }}</h1>
        <p class="text-gray-600 mt-2">{{ $customer['description'] ?? 'No description available' }}</p>
    </div>

    <!-- Epics by Fix Version -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Features by Fix Version</h2>
        @if (!empty($epics))
            @foreach ($epics as $fixVersion => $epicsInVersion)
            <div class="bg-white p-4 rounded shadow mb-4">
                <h3 class="text-lg font-semibold">{{ $fixVersion }}</h3>
                <ul class="mt-2 space-y-2">
                    @foreach ($epicsInVersion as $epic)
                    <li class="p-3 bg-gray-50 rounded shadow">
                        <a href="{{ route('epics.show', $epic['key'] ?? '') }}" class="font-bold text-blue-600">
                            {{ $epic['fields']['summary'] ?? 'No summary available' }}
                        </a>
                        <p class="text-sm text-gray-600">Key: {{ $epic['key'] ?? 'No key available' }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        @else
            <p>No epics found for this customer.</p>
        @endif
    </div>

    <!-- Bugs -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Bugs</h2>
        @if (!empty($bugs))
            <ul>
                @foreach ($bugs as $bug)
                <li class="p-3 bg-red-50 rounded shadow">
                    {{ $bug['fields']['summary'] ?? 'No summary available' }}
                </li>
                @endforeach
            </ul>
        @else
            <p>No bugs for this customer.</p>
        @endif
    </div>

    <!-- Requests -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Requests</h2>
        @if (!empty($requests))
            <ul>
                @foreach ($requests as $request)
                <li class="p-3 bg-blue-50 rounded shadow">
                    {{ $request['fields']['summary'] ?? 'No summary available' }}
                </li>
                @endforeach
            </ul>
        @else
            <p>No requests for this customer.</p>
        @endif
    </div>
</div>
</x-app-layout>
