<div>
    <header class="bg-gray-50 shadow-lg border">
        <div class="flex items-center justify-between px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ __('Customers') }}
            </h2>
        <input wire:model.live="search" type="text" placeholder="Search customers..." class="border rounded px-3 py-1 mb-4">
        </div>
    </header>

    <div class="container mx-auto py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-2">
            @foreach ($customers as $customer)
                <a href="{{ route('customers.show', $customer->id) }}" class="block bg-white p-6 rounded shadow hover:bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-bold">{{ $customer->name }}</h2>
                            <p class="text-sm text-gray-500">ID: {{ $customer->id }}</p>
                        </div>

                        @php
                            $typeCounts = $customer->issues->groupBy('type')->map->count();
                            $total = $typeCounts->sum();
                        @endphp
                        <div class="flex gap-2">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">{{ $typeCounts['Epic'] ?? 0 }} Epics</span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">{{ $typeCounts['Bug'] ?? 0 }} Bugs</span>
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">{{ $typeCounts['Request'] ?? 0 }} Requests</span>
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded font-medium">{{ $total }} Total</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
