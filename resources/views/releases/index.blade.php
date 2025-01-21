<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">
            {{ __('Releases Overview') }}
        </h1>
    </x-slot>
    <div class="container mx-auto py-6 space-y-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($releases as $release)
                <div class="bg-white p-6 rounded shadow space-y-4">
                    <!-- Release Title -->
                    <h2 class="text-2xl font-bold text-gray-800">{{ $release['name'] }}</h2>
                    <p class="text-sm text-gray-500">Release Date: {{ $release['releaseDate'] ?? 'TBD' }}</p>

                    <!-- Epics Table -->
                    <div>
                        <h3 class="text-lg mb-3 font-bold flex items-center">
                            Epics
                            <span class="ml-2 px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded">
                                {{ count($release['epics']) }}
                            </span>
                        </h3>
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="text-left bg-gray-100">
                                    <th class="px-4 py-2 text-xs">Status</th>
                                    <th class="px-4 py-2 text-xs">Count</th>
                                    <th class="px-4 py-2 text-xs text-center">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($release['epicStatusCounts'] as $status => $count)
                                    <tr class="border-b">
                                        <td class="flex items-center gap-2 px-4 py-2 text-sm">
                                            <span class="w-3 h-3 rounded-full {{ $statusColors[$status] ?? 'bg-gray-300' }}"></span>
                                            {{ $status }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $count }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('releases.statusDetails', ['releaseKey' => $release['id'], 'type' => 'epics', 'status' => $status]) }}"
                                            class="text-gray-300 hover:underline">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Issues Table -->
                    <div class="mt-6">
                        <h3 class="text-lg mb-3 font-bold flex items-center">
                            Tickets
                            <span class="ml-2 px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded">
                                {{ $release['issueCount'] }}
                            </span>
                        </h3>
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="text-left bg-gray-100">
                                    <th class="px-4 py-2 text-xs">Status</th>
                                    <th class="px-4 py-2 text-xs">Count</th>
                                    <th class="px-4 py-2 text-xs text-center">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($release['issueStatusCounts'] as $status => $count)
                                    <tr class="border-b">
                                        <td class="flex items-center gap-2 px-4 py-2 text-sm">
                                            <span class="w-3 h-3 rounded-full {{ $statusColors[$status] ?? 'bg-gray-300' }}"></span>
                                            {{ $status }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $count }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('releases.statusDetails', ['releaseKey' => $release['id'], 'type' => 'issues', 'status' => $status]) }}"
                                            class="text-gray-300 hover:underline">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Customers -->
                    <div>
                        <h3 class="text-lg font-bold mb-2">Customers</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($release['uniqueCustomers'] as $customer)
                                <a href="{{ route('customers.show', $customer['id']) }}"
                                class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200">
                                    {{ $customer['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Risk-Watch Epics -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Risk-Watch Epics</h3>
                        @if (!empty($release['riskWatchEpics']))
                            <div class="space-y-1">
                                @foreach ($release['riskWatchEpics'] as $epic)
                                    <a href="{{ route('epics.show', $epic['key']) }}" class="bg-white shadow p-2 flex justify-between items-center">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ $epic['fields']['summary'] }}
                                        </h4>
                                        <span class="text-gray-300 hover:text-brand-800">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No risk-watch epics found.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
