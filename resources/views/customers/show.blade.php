<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Customer Header -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $customer['name'] }}</h1>
                    <p class="text-gray-600 mt-2">{{ $customer['description'] ?? 'No description available' }}</p>
                </div>
                <div class="space-x-4">
                    <span class="bg-brand-200 text-brand-800 px-3 py-1 rounded-full text-sm font-medium">
                        Epics: {{ $counts['epics'] }}
                    </span>
                    <span class="bg-red-200 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                        Bugs: {{ $counts['bugs'] }}
                    </span>
                    <span class="bg-blue-200 text-brand-800 px-3 py-1 rounded-full text-sm font-medium">
                        Requests: {{ $counts['requests'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Grouped Items by Fix Version -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Items by Fix Version</h2>
            @if (!empty($groupedItems))
                @foreach ($groupedItems as $fixVersion => $items)
                    <div class="bg-white p-4 rounded shadow mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">{{ $fixVersion }}</h3>
                        <ul class="mt-3 space-y-3">
                            @php
                                // Sort items by type: Bug, then Epic, then Request
                                usort($items, function ($a, $b) {
                                    $typeOrder = ['Bug' => 1, 'Epic' => 2, 'Request' => 3];
                                    $typeA = $typeOrder[$a['fields']['issuetype']['name']] ?? 99;
                                    $typeB = $typeOrder[$b['fields']['issuetype']['name']] ?? 99;
                                    return $typeA <=> $typeB;
                                });
                            @endphp
                            @foreach ($items as $item)
                                <li class="p-4 rounded shadow {{ $item['fields']['issuetype']['name'] === 'Bug' ? 'bg-red-50' : ($item['fields']['issuetype']['name'] === 'Request' ? 'bg-blue-50' : 'bg-gray-50') }}">
                                    <div class="flex justify-between items-center">
                                        <a href="{{ route('epics.show', $item['key']) }}" class="font-bold {{ $item['fields']['issuetype']['name'] === 'Bug' ? 'text-red-800' : ($item['fields']['issuetype']['name'] === 'Request' ? 'text-blue-800' : 'text-brand-600') }}">
                                            {{ $item['fields']['summary'] ?? 'No summary available' }}
                                        </a>
                                        <a href="https://cityinnovate.atlassian.net/browse/{{ $item['key'] }}"
                                        class="text-xs text-gray-600 underline" target="_blank">
                                            View on Jira
                                        </a>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Key: {{ $item['key'] ?? 'No key available' }} |
                                        Type: {{ $item['fields']['issuetype']['name'] }} |
                                        Priority: {{ $item['fields']['priority']['name'] ?? 'Unknown' }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <p class="text-gray-600">No items found for this customer.</p>
            @endif
        </div>

        <!-- Ungrouped Items -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Items Without a Fix Version</h2>
            @if (!empty($unassignedItems))
                <div class="bg-white p-4 rounded shadow mb-4">
                    <ul class="space-y-3">
                        @php
                            // Sort unassigned items by type: Bug, then Epic, then Request
                            usort($unassignedItems, function ($a, $b) {
                                $typeOrder = ['Bug' => 1, 'Epic' => 2, 'Request' => 3];
                                $typeA = $typeOrder[$a['fields']['issuetype']['name']] ?? 99;
                                $typeB = $typeOrder[$b['fields']['issuetype']['name']] ?? 99;
                                return $typeA <=> $typeB;
                            });
                        @endphp
                        @foreach ($unassignedItems as $item)
                            <li class="p-4 rounded shadow {{ $item['fields']['issuetype']['name'] === 'Bug' ? 'bg-red-50' : ($item['fields']['issuetype']['name'] === 'Request' ? 'bg-blue-50' : 'bg-gray-50') }}">
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('epics.show', $item['key']) }}" class="font-bold {{ $item['fields']['issuetype']['name'] === 'Bug' ? 'text-red-800' : ($item['fields']['issuetype']['name'] === 'Request' ? 'text-blue-800' : 'text-brand-600') }}">
                                        {{ $item['fields']['summary'] ?? 'No summary available' }}
                                    </a>
                                    <a href="https://cityinnovate.atlassian.net/browse/{{ $item['key'] }}"
                                       class="text-xs text-gray-600 underline" target="_blank">
                                        View on Jira
                                    </a>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    Key: {{ $item['key'] ?? 'No key available' }} | Type: {{ $item['fields']['issuetype']['name'] }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-gray-600">No ungrouped items found for this customer.</p>
            @endif
        </div>

        <!-- Previously Shipped Work -->
        @if (!empty($shippedItems))
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Shipped Work (Released Versions)</h2>

            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100 text-left text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Fix Version</th>
                            <th class="px-4 py-2">Key</th>
                            <th class="px-4 py-2">Summary</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Priority</th>
                            <th class="px-4 py-2">Jira</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($shippedItems as $version => $items)
                            @foreach ($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-medium text-gray-800">{{ $version }}</td>
                                    <td class="px-4 py-2">{{ $item['key'] ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $item['fields']['summary'] ?? 'No summary' }}</td>
                                    <td class="px-4 py-2">{{ $item['fields']['issuetype']['name'] ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $item['fields']['priority']['name'] ?? '—' }}</td>
                                    <td class="px-4 py-2">
                                        <a href="https://cityinnovate.atlassian.net/browse/{{ $item['key'] }}" class="text-blue-600 underline text-xs" target="_blank">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        @endif
    </div>
</x-app-layout>
