<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Header -->
        <x-slot name="header">
            <h1 class="text-xl font-semibold text-gray-900">
                {{ ucfirst($type) }} in "{{ $release['name'] }}" - Status: "{{ $status }}"
            </h1>
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-brand-100 text-brand-800 rounded">
                {{ count($items) }} {{ ucfirst($type) }}
            </span>
            <a href="{{ route('releases.workload', $releaseKey) }}"><i class="fa-regular fa-dumbbell"></i></a>
        </x-slot>

        <!-- Items Table -->
        <div class="bg-white p-6 rounded shadow">
            <table id="itemsTable" class="stripe">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="border px-4 py-2">Key</th>
                        <th class="border px-4 py-2 text-xs">Summary</th>
                        <th class="border px-4 py-2 text-xs">Priority</th>
                        @if ($type === 'epics')
                            <th class="border px-4 py-2 text-xs">Customer Commitment</th>
                            <th class="border px-4 py-2 text-xs">Size</th>
                            <th class="border px-4 py-2 text-xs">Parent</th>
                        @endif
                        <th class="border px-4 py-2 text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="border px-4 py-2">
                                    <a href="https://cityinnovate.atlassian.net/browse/{{ $item['key'] }}" target="_blank" class="text-brand-600 hover:underline">
                                        {{ $item['key'] }}
                                    </a>
                                </td>
                            <td class="border px-4 py-2">
                                <a href="{{ $type === 'epics' ? route('epics.show', $item['key']) : 'https://cityinnovate.atlassian.net/browse/' . $item['key'] }}"
                                   class="text-brand-600 font-medium hover:underline">
                                    {{ $item['fields']['summary'] ?? 'No Summary' }}
                                </a>
                            </td>
                            <td class="border px-4 py-2 text-sm">{{ $item['fields']['priority']['name'] ?? '--' }}</td>
                            @if ($type === 'epics')
                                <td class="border px-4 py-2 text-sm">
                                    {{ $item['fields']['customfield_10473']['value'] ?? '--' }}
                                </td>
                                <td class="border px-4 py-2 text-sm">
                                    {{ $item['fields']['customfield_10507']['value'] ?? '--' }}
                                </td>
                                <td class="border px-4 py-2 text-sm">
                                    {{ $item['fields']['parent']['fields']['summary'] ?? 'No Parent' }}
                                </td>
                            @endif
                            <td class="border px-4 py-2 text-sm">
                                <a href="{{ $type === 'epics' ? route('epics.show', $item['key']) : 'https://cityinnovate.atlassian.net/browse/' . $item['key'] }}"
                                   class="text-brand-600 text-sm hover:underline">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#itemsTable').DataTable({
                paging: true,
                pageLength: 50,
                searching: true,
                order: [[1, 'asc']] // Default sort by second column (Priority)
            });
        });
    </script>
</x-app-layout>
