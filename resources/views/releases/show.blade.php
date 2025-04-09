<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Release Header Card -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $releaseName }}</h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Release Key: <span class="font-semibold">{{ $releaseKey }}</span>
                        <a href="{{ route('releases.workload', $releaseKey) }}"><i class="fa-regular fa-dumbbell"></i></a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Epics Table Card -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Epics in Release</h2>
            <table id="epicsTable" class="table-auto w-full border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm">
                        <th class="border px-4 py-2 text-left">Key</th>
                        <th class="border px-4 py-2 text-left">Name</th>
                        <th class="border px-4 py-2 text-left">Size</th>
                        <th class="border px-4 py-2 text-left">Status</th>
                        <th class="border px-4 py-2 text-left">Priority</th>
                        <th class="border px-4 py-2 text-left">Customer Commitment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($release->issues->where('type', 'Epic')->sortBy('status') as $epic)
                        <tr class="odd:bg-gray-50 even:bg-white">
                            <!-- Epic Key -->
                            <td class="border px-4 py-2 text-medium">
                                <a href="https://cityinnovate.atlassian.net/browse/{{ $epic->jira_key }}" target="_blank" class="text-brand-600 hover:underline">
                                    {{ $epic->jira_key }}
                                </a>
                            </td>
                            <!-- Epic Name -->
                            <td class="border px-4 py-2">
                                <a href="{{ route('epics.show', $epic->jira_key) }}" class="text-brand-600 hover:underline">
                                    {{ $epic->summary }}
                                </a>
                            </td>
                            <!-- Size -->
                            <td class="border px-4 py-2 text-sm">
                                {{ $epic->size ?? '--' }}
                            </td>
                            <!-- Status -->
                            <td class="border px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded text-sm font-medium
                                    {{ $epic->status === 'Done' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $epic->status ?? 'Unknown' }}
                                </span>
                            </td>
                            <!-- Priority -->
                            <td class="border px-4 py-2">{{ $epic->priority ?? '--' }}</td>
                            <!-- Customer Commitment -->
                            <td class="border px-4 py-2">{{ $epic->release_commit_status ?? 'None' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-2 text-center text-gray-500 italic">
                                No epics found for this release.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DataTable Script -->
    <script>
        $(document).ready(function () {
            $('#epicsTable').DataTable({
                paging: true,
                pageLength: 50,
                searching: true,
                order: [[4, 'asc']], // Default sort by status
            });
        });
    </script>
</x-app-layout>
