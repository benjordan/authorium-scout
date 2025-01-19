<x-app-layout>
    <h1 class="text-2xl font-bold">Releases</h1>
    <ul>
        @foreach($releases as $release)
            <li>
                <a href="{{ route('releases.show', $release['id']) }}" class="text-blue-500">
                    {{ $release['name'] }} ({{ $release['releaseDate'] }})
                </a>
            </li>
        @endforeach
    </ul>
</x-app-layout>
