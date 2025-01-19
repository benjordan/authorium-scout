<x-app-layout>
    <div class="flex items-center justify-between px-6 py-4 bg-gray-800">
        <h1 class="text-xl font-bold text-gray-100">Customers</h1>
    </div>

    <div class="container mx-auto py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($customers as $id => $name)
            <a href="{{ route('customers.show', $id) }}" class="block bg-white p-4 rounded shadow hover:bg-gray-50">
                <h2 class="text-lg font-bold">{{ $name }}</h2>
            </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
