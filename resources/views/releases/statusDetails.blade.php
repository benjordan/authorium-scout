<x-app-layout>
    <div class="container mx-auto py-6">
        <x-slot name="header">
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $release['name'] }}
            </h1>
        </x-slot>

        {{-- Items Table --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold border-b text-gray-900 pb-3 mb-6">
                {{ $status }}
            </h2>
            <x-issue-table :items="$items" tableId="epicsTable" />
        </div>
    </div>

</x-app-layout>
