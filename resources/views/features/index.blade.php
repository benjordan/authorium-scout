<x-app-layout>

    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-100">
            {{ __('Features') }}
        </h1>
    </x-slot>

    <div class="container mx-auto py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($features as $feature)
                <a href="{{ route('features.show', $feature['id']) }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
                    <h2 class="text-lg font-bold">{{ $feature['name'] }}</h2>
                    <p class="text-gray-600">{{ $feature['description'] ?? 'No description' }}</p>
                </a>
            @endforeach
        </div>
    </div>

</x-app-layout>
