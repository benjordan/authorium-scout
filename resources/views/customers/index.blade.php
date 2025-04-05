<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">
            {{ __('Customers') }}
        </h1>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($customers as $id => $customer)
                <div class="block bg-white p-4 rounded shadow hover:bg-gray-50">
                    <a href="{{ route('customers.show', $id) }}" class="block">
                        <h2 class="text-lg font-bold">{{ $customer['name'] }}</h2>
                    </a>

                    <!-- Counts -->
                    <div class="mt-4 space-y-2">
                        <span class="bg-brand-100 text-brand-800 px-3 py-1 mr-2 rounded-full text-sm font-medium">
                            Epics: {{ $customer['counts']['epics'] ?? 0 }}
                        </span>
                        <span class="bg-red-100 text-red-800 px-3 py-1 mr-2 rounded-full text-sm font-medium">
                            Bugs: {{ $customer['counts']['bugs'] ?? 0 }}
                        </span>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 mr-2 rounded-full text-sm font-medium">
                            Requests: {{ $customer['counts']['requests'] ?? 0 }}
                        </span>
                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                            Total Items: {{ $customer['counts']['total'] ?? 0 }}
                        </span>
                    </div>

                    <!-- Fix Versions Table -->
                    @if (!empty($customer['fixVersions']))
                        <div class="mt-4">
                            <table class="table-auto w-full border border-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-2 py-1 text-left">Fix Version</th>
                                        <th class="border px-2 py-1 text-left">Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer['fixVersions'] as $fixVersion => $count)
                                        <tr>
                                            <td class="border px-2 py-1">{{ $fixVersion }}</td>
                                            <td class="border px-2 py-1">{{ $count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
