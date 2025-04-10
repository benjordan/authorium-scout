<x-app-layout>
    <div class="container mx-auto py-6">
        <x-slot name="header">
            <h1 class="text-xl font-semibold text-gray-900">
                            {{ ucfirst($type) }} in "{{ $release['name'] }}" - Status: "{{ $status }}"
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                        Workload: <a href="{{ route('releases.workload', $release->id) }}"><i class="fa-regular fa-dumbbell"></i></a>
                    </p>
        </x-slot>

        {{-- Items Table --}}
        <div class="bg-white p-6 rounded shadow">
            <x-issue-table :items="$items" tableId="epicsTable" />
        </div>
    </div>

</x-app-layout>
