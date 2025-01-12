<x-layout>
    <h1 class="text-2xl font-bold mb-4">Unreleased Releases</h1>
    <ul>
        @foreach($releases as $release)
            <li class="border-b p-2">
                <a href="{{ route('release-epics', $release['id']) }}" class="text-blue-500">
                    {{ $release['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</x-layout>
