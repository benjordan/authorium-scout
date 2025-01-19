<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold">Open Epics</h1>
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-800 rounded">
                {{ count($epics) }} Epics
            </span>
        </div>

        <!-- Epic Table -->
        <div class="bg-white p-6 rounded shadow">
            <table id="epicsTable" class="stripe">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="border px-4 py-2">Summary</th>
                        <th class="border px-4 py-2">Priority</th>
                        <th class="border px-4 py-2">Customer Commitment</th>
                        <th class="border px-4 py-2">Size</th>
                        <th class="border px-4 py-2">Parent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($epics as $epic)
                        <tr>
                            <td class="border px-4 py-2">
                                <a href="{{ route('epics.show', $epic['key']) }}" class="text-blue-600 hover:underline">
                                    {{ $epic['fields']['summary'] }}
                                </a>
                            </td>
                            <td class="border px-4 py-2">{{ $epic['fields']['priority']['name'] }}</td>
                            <td class="border px-4 py-2">
                                {{ $epic['fields']['customfield_10473']['value'] ?? '--' }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ $epic['fields']['customfield_10507']['value'] ?? '--' }}
                            </td>
                            <td class="border px-4 py-2">
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
