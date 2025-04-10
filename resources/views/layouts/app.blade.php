<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Release Views') }}</title>

        @livewireStyles

        <!-- Fonts -->
        @googlefonts

        <!-- Scripts -->
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
        <script src="https://kit.fontawesome.com/7669ab7fa3.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Tom Select -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        <div class="flex flex-col min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-50 shadow-lg border">
                    <div class="flex items-center justify-between px-6 py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow bg-repeat" style="background-image: url('/img/topographic-bg.png');">
                {{ $slot }}
            </main>

            <footer class="w-full text-center text-xs bg-gray-700 text-gray-100 py-6 border-t mt-12">
                {{ $appVersion }} &nbsp; &bull; &nbsp;
                Jira data last synced {{ $jiraLastSynced }}.
            </footer>
        </div>

    </body>
    @livewireScripts
    @stack('scripts')
    <script>
        // Dropdown Toggle
        document.getElementById('user-menu-button').addEventListener('click', () => {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</html>
