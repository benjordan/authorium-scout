<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <x-slot name="header">
            <h1 class="text-xl font-semibold text-gray-900">Open Epics</h1>
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-brand-100 text-brand-800 rounded">
                {{ count($epics) }} Epics
            </span>
        </x-slot>

        <!-- Epic Table -->
        <div class="bg-white p-6 rounded shadow">
            <table id="epicsTable" class="stripe">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border px-4 py-2 text-sm">Summary</th>
                        <th class="border px-4 py-2 text-sm">Priority</th>
                        <th class="border px-4 py-2 text-sm">Customer Commitment</th>
                        <th class="border px-4 py-2 text-sm">Size</th>
                        <th class="border px-4 py-2 text-sm">Parent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($epics as $epic)
                        <tr>
                            <td class="border px-4 py-2 font-medium">
                                <a href="{{ route('epics.show', $epic['key']) }}" class="text-brand-600 hover:underline">
                                    {{ $epic['fields']['summary'] }}
                                </a>
                            </td>
                            <td class="border px-4 py-2 text-sm">{{ $epic['fields']['priority']['name'] }}</td>
                            <td class="border px-4 py-2 text-sm">
                                {{ $epic['fields']['customfield_10473']['value'] ?? '--' }}
                            </td>
                            <td class="border px-4 py-2 text-sm">
                                {{ $epic['fields']['customfield_10507']['value'] ?? '--' }}
                            </td>
                            <td class="border px-4 py-2 text-sm">
                                {{ $epic['fields']['parent']['fields']['summary'] ?? 'No Parent' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#epicsTable').DataTable({
                paging: true,
                pageLength: 50,
                searching: true,
                order: [[1, 'asc']] // Default sort by second column (Priority)
            });
        });
    </script>
</x-app-layout>
