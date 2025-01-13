<x-layout>
    <h1 class="text-2xl font-bold mb-4">Epics in Release: {{ $releaseName }}</h1>
    <ul>
        @foreach($epics as $epic)
            <li class="border-b p-2">
                <a href="{{ route('epic-details', $epic['key']) }}" class="text-blue-500">
                    {{ $epic['fields']['summary'] }}
                </a>
                <span class="text-sm text-gray-600">
                    (Priority: {{ $epic['fields']['priority']['name'] }})
                </span>
            </li>
        @endforeach
    </ul>
</x-layout>
