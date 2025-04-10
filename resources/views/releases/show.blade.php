<x-app-layout>

    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $releaseName }}</h1>
         <p class="mt-2 text-sm text-gray-500">
            <a href="{{ route('releases.workload', $release->id) }}"><i class="fa-regular fa-dumbbell"></i></a>
        </p>
    </x-slot>

    <div class="container mx-auto py-6 space-y-8">

        <!-- Epics Table Card -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-medium border-b text-gray-900 pb-3 mb-6">Epics in Release</h2>
            <x-issue-table :items="$release->issues->where('type', 'Epic')->sortBy('status')" tableId="epicsTable" />
        </div>

    </div>
</x-app-layout>
