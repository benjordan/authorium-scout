<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold">
                {{ ucfirst($type) }} in "{{ $release['name'] }}" - Status: "{{ $status }}"
            </h1>
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-brand-100 text-brand-800 rounded">
                {{ count($items) }} {{ ucfirst($type) }}
            </span>
        </div>

        <!-- Items Table -->
        <div class="bg-white p-6 rounded shadow">
            <table id="itemsTable" class="stripe">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="border px-4 py-2">Summary</th>
                        <th class="border px-4 py-2">Priority</th>
                        @if ($type === 'epics')
                            <th class="border px-4 py-2">Customer Commitment</th>
                            <th class="border px-4 py-2">Size</th>
                            <th class="border px-4 py-2">Parent</th>
                        @endif
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td class="border px-4 py-2">
                                <a href="{{ $type === 'epics' ? route('epics.show', $item['key']) : 'https://cityinnovate.atlassian.net/browse/' . $item['key'] }}"
                                   class="text-blue-600 hover:underline">
                                    {{ $item['fields']['summary'] ?? 'No Summary' }}
                                </a>
                            </td>
                            <td class="border px-4 py-2">{{ $item['fields']['priority']['name'] ?? '--' }}</td>
                            @if ($type === 'epics')
                                <td class="border px-4 py-2">
                                    {{ $item['fields']['customfield_10473']['value'] ?? '--' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $item['fields']['customfield_10507']['value'] ?? '--' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $item['fields']['parent']['fields']['summary'] ?? 'No Parent' }}
                                </td>
                            @endif
                            <td class="border px-4 py-2">
                                <a href="{{ $type === 'epics' ? route('epics.show', $item['key']) : 'https://cityinnovate.atlassian.net/browse/' . $item['key'] }}"
                                   class="text-blue-600 hover:underline">
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
