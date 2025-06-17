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
                Stories: {{ $counts['Story'] ?? 0 }}
            </span>
        </div>

    </x-slot>

    <div class="container mx-auto py-6 space-y-8">

        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-1">Committed Work</h2>
            <p class="text-sm text-gray-600 mb-4">Work that has been committed to a specific release and has assigned fix versions.</p>

            @if($groupedIssues['committed']->count() > 0)
                <div class="bg-white p-6 rounded shadow mb-4">
                    <x-issue-table :items="$groupedIssues['committed']" tableId="table_committed_work" :showFixVersion="true" />
                </div>
            @else
                <p class="text-gray-600">No committed work found for this customer.</p>
            @endif
        </div>

        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-1">Open Work</h2>
            <p class="text-sm text-gray-600 mb-4">All other work that is not yet committed to a specific release.</p>

            @if($groupedIssues['open']->count() > 0)
                <div class="bg-white p-6 rounded shadow mb-4">
                    <x-issue-table :items="$groupedIssues['open']" tableId="table_open_work" :showFixVersion="false" />
                </div>
            @else
                <p class="text-gray-600">No open work found for this customer.</p>
            @endif
        </div>

    </div>

</x-app-layout>
