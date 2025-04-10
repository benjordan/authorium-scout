@props(['items', 'tableId' => 'issuesTable'])

<table id="{{ $tableId }}" class="table-auto w-full border border-gray-200">
    <thead>
        <tr class="bg-gray-100 text-gray-600 text-sm">
            <th class="border px-4 py-2 text-left whitespace-nowrap">Key</th>
            <th class="border px-4 py-2 text-left">Name</th>
            <th class="border px-4 py-2 text-left">Type</th>
            <th class="border px-4 py-2 text-left">Size</th>
            <th class="border px-4 py-2 text-left">Status</th>
            <th class="border px-4 py-2 text-left">Priority</th>
            <th class="border px-4 py-2 text-left">Customer Commitment</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
            <tr class="odd:bg-gray-50 even:bg-white">
                <td class="border px-4 py-2 text-sm">
                    <a href="https://cityinnovate.atlassian.net/browse/{{ $item->jira_key }}" target="_blank" class="text-brand-600 hover:underline">
                        {{ $item->jira_key }}
                    </a>
                </td>
                <td class="border px-4 py-2">
                    <a href="{{ route('epics.show', $item->jira_key) }}" class="text-brand-600 font-medium hover:underline">
                        {{ $item->summary }}
                    </a>
                </td>
                <td class="border px-4 py-2 text-sm">
                    @php
                        $type = strtolower($item->type);
                        $typeStyles = [
                            'epic' => 'bg-purple-100 text-purple-800',
                            'request' => 'bg-orange-100 text-orange-800',
                            'bug' => 'bg-red-100 text-red-800',
                            'story' => 'bg-blue-100 text-blue-800',
                        ];
                        $badgeClass = $typeStyles[$type] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $badgeClass }}">
                        {{ ucfirst($item->type) }}
                    </span>
                </td>
                <td class="border px-4 py-2 text-sm">
                    {{ $item->size ?? '--' }}
                </td>
                <td class="border px-4 py-2">
                    <span class="inline-block px-2 py-1 rounded text-sm font-medium
                        {{ $item->status === 'Done' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $item->status ?? 'Unknown' }}
                    </span>
                </td>
                <td class="border px-4 py-2 text-sm">{{ $item->priority ?? '--' }}</td>
                <td class="border px-4 py-2 text-sm">{{ $item->release_commit_status ?? 'None' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="border px-4 py-2 text-center text-gray-500 italic">
                    No items found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#{{ $tableId }}').DataTable({
            paging: true,
            pageLength: 10,
            searching: true,
            order: [[0, 'desc']],
            columnDefs: [
                { targets: 0, width: '90px' } // target first column (Key), fixed width
            ]
        });
    });
</script>
