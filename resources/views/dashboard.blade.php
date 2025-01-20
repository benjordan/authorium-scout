<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">
            {{ __('Dashboard') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Just like all good apps, the dashboard always comes last.") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
