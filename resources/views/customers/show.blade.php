<x-app-layout>

    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $customer->name }}</h1>
        <div class="space-x-2">
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
    </x-slot>

    <div class="container mx-auto py-6 space-y-8">

        <!-- Grouped Items by Fix Version -->
        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-4">Items by Fix Version</h2>
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
