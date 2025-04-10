<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Customer Header -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $customer->name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $customer->description ?? 'No description available' }}</p>
                </div>
                <div class="space-x-4">
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded text-sm font-medium">
                        Epics: {{ $counts['Epic'] ?? 0 }}
                    </span>
                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded text-sm font-medium">
                        Requests: {{ $counts['Request'] ?? 0 }}
                    </span>
                    <span class="bg-red-200 text-red-800 px-3 py-1 rounded text-sm font-medium">
                        Bugs: {{ $counts['Bug'] ?? 0 }}
                    </span>
                    <span class="bg-blue-100 text-brand-800 px-3 py-1 rounded text-sm font-medium">
                        Requests: {{ $counts['Story'] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Grouped Items by Fix Version -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Items by Fix Version</h2>
            @if (!empty($groupedIssues))
                @foreach ($groupedIssues as $fixVersion => $items)
                    <div class="bg-white p-6 rounded shadow mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">{{ $fixVersion }}</h3>
                        <x-issue-table :items="$items" tableId="table_{{ Str::slug($fixVersion) }}" />
                    </div>
                @endforeach
            @else
                <p class="text-gray-600">No items found for this customer.</p>
            @endif
        </div>
    </div>

</x-app-layout>
