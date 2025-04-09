<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">

        {{-- Header --}}
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">
                        {{ ucfirst($type) }} in "{{ $release['name'] }}" - Status: "{{ $status }}"
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 mt-1 text-sm font-medium bg-brand-100 text-brand-800 rounded">
                        {{ count($items) }} {{ ucfirst($type) }}
                    </span>
                </div>
                <a href="{{ route('releases.workload', $release['id']) }}" class="text-brand-600 hover:text-brand-800">
                    <i class="fa-regular fa-dumbbell text-xl"></i>
                </a>
            </div>
        </x-slot>

        {{-- Items Table --}}
        <div class="bg-white p-6 rounded shadow">
            <table id="itemsTable" class="stripe w-full">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-left text-xs">
                        <th class="border px-4 py-2">Key</th>
                        <th class="border px-4 py-2">Summary</th>
                        <th class="border px-4 py-2">Priority</th>
                        @if ($type === 'epics')
                            <th class="border px-4 py-2">Size</th>
                        @endif
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            {{-- Key --}}
                            <td class="border px-4 py-2">
                                <a href="https://cityinnovate.atlassian.net/browse/{{ $item->jira_key }}" target="_blank"
                                   class="text-brand-600 hover:underline">
                                    {{ $item->jira_key }}
                                </a>
                            </td>

                            {{-- Summary --}}
                            <td class="border px-4 py-2">
                                <a href="{{ $type === 'epics' ? route('epics.show', $item->jira_key) : 'https://cityinnovate.atlassian.net/browse/' . $item->jira_key }}"
                                   class="text-brand-600 font-medium hover:underline">
                                    {{ $item->summary ?? 'No Summary' }}
                                </a>
                            </td>

                            {{-- Priority --}}
                            <td class="border px-4 py-2 text-sm">
                                {{ $item->priority ?? '--' }}
                            </td>

                            {{-- Epics Only Columns --}}
                            @if ($type === 'epics')
                                <td class="border px-4 py-2 text-sm">
                                    {{ $item->size ?? '--' }}
                                </td>
                            @endif

                            {{-- Actions --}}
                            <td class="border px-4 py-2 text-sm">
                                <a href="{{ $type === 'epics' ? route('epics.show', $item->jira_key) : 'https://cityinnovate.atlassian.net/browse/' . $item->jira_key }}"
                                   class="text-brand-600 hover:underline">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- DataTables Init --}}
    <script>
        $(document).ready(function () {
            $('#itemsTable').DataTable({
                paging: true,
                pageLength: 50,
                searching: true,
                order: [[1, 'asc']] // Sort by Summary column
            });
        });
    </script>
</x-app-layout>
