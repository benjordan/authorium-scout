<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Unreleased Releases') }}
        </h2>
    </x-slot>

    <ul>
        @foreach($releases as $release)
            <li class="border-b p-2">
                <a href="{{ route('release-epics', $release['id']) }}" class="text-blue-500">
                    {{ $release['name'] }}
                </a>
            </li>
        @endforeach
    </ul>

</x-app-layout>
