<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">
            {{ __('Releases Overview') }}
        </h1>
        <span>
            <a href="{{ route('kanban') }}">
                <button type="button"
                    class="rounded px-2 py-1 text-sm font-semibold border border-brand-500 text-brand-600 shadow-sm hover:bg-brand-100">
                    Kanban View
                </button>
            </a>
        </span>
    </x-slot>

    <div class="container mx-auto py-6 space-y-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($releases as $release)
                <div class="bg-white p-6 rounded shadow space-y-4">
                    <!-- Release Title -->
                    <a href="{{ route('releases.show', $release->release->id) }}">
                        <h2 class="text-2xl font-bold text-brand-600">{{ $release->release->name }}</h2>
                    </a>
                    <p class="text-sm text-gray-500">Release Date: {{ $release->release->release_date ?? 'TBD' }}</p>

                    <!-- Epics -->
                    <div>
                        <h3 class="text-lg mb-3 font-bold flex items-center">
                            Epics
                            <span class="ml-2 px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded">
                                {{ $release->epics_by_status->sum() }}
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
                                @foreach ($release->epics_by_status as $status => $count)
                                    <tr class="border-b">
                                        <td class="flex items-center gap-2 px-4 py-2 text-sm">
                                            <span class="w-3 h-3 rounded-full {{ $statusColors[$status] ?? 'bg-gray-300' }}"></span>
                                            {{ $status }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $count }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('releases.statusDetails', ['releaseKey' => $release->release->id, 'type' => 'epics', 'status' => $status]) }}"
                                               class="text-brand-600 hover:underline">
                                                <i class="fas fa-eye"></i>
                                                <span class="sr-only">View</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tickets -->
                    <div class="mt-6">
                        <h3 class="text-lg mb-3 font-bold flex items-center">
                            Tickets
                            <span class="ml-2 px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded">
                                {{ $release->tickets_by_status->sum() }}
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
                                @foreach ($release->tickets_by_status as $status => $count)
                                    <tr class="border-b">
                                        <td class="flex items-center gap-2 px-4 py-2 text-sm">
                                            <span class="w-3 h-3 rounded-full {{ $statusColors[$status] ?? 'bg-gray-300' }}"></span>
                                            {{ $status }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $count }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('releases.statusDetails', ['releaseKey' => $release->release->id, 'type' => 'issues', 'status' => $status]) }}"
                                               class="text-brand-600 hover:underline">
                                                <i class="fas fa-eye"></i>
                                                <span class="sr-only">View</span>
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
                            @foreach($release->customers as $customer)
                                <a href="{{ route('customers.show', $customer->id) }}"
                                   class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200">
                                    {{ $customer->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Risk-Watch Epics -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Risk-Watch Epics</h3>
                        @php
                            $riskWatch = $release->release->issues->where('type', 'Epic')->filter(fn($epic) => in_array('risk-watch', $epic->labels ?? []));
                        @endphp
                        @if ($riskWatch->isNotEmpty())
                            <div class="space-y-1">
                                @foreach ($riskWatch as $epic)
                                    <a href="{{ route('epics.show', ['jira_key' => $epic->jira_key]) }}"
                                       class="bg-white shadow p-2 flex justify-between items-center">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ $epic->summary }}
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
