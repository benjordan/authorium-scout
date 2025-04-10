<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">

        <!-- Release Header Card -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $releaseName }}</h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Release Key: <span class="font-semibold">{{ $releaseKey }}</span>
                        <a href="{{ route('releases.workload', $release->id) }}"><i class="fa-regular fa-dumbbell"></i></a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Epics Table Card -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-bold mb-4">Epics in Release</h2>
            <x-issue-table :items="$release->issues->where('type', 'Epic')->sortBy('status')" tableId="epicsTable" />
        </div>

    </div>
</x-app-layout>
