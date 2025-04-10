<x-app-layout>

    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">Open Epics</h1>
        <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-brand-100 text-brand-800 rounded">
            {{ count($epics) }} Epics
        </span>
    </x-slot>

    <div class="container mx-auto py-6 space-y-8">
        <!-- Epic Table -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-medium border-b text-gray-900 pb-3 mb-6">Open Epics</h2>
            <x-issue-table :items="$epics" tableId="epicsTable" />
        </div>
    </div>
</x-app-layout>
