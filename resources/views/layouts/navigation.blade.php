<header class="bg-white shadow-sm">
    <div class="mx-auto px-4 py-5 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
            <a href="/" class="text-xl font-bold text-gray-900">
                <img src="/img/Atlas-Logo-Color-Small.png" alt="Atlas Logo" class="h-10 w-auto mr-2 inline-block">
                Epics Atlas
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 hidden md:flex items-center justify-center space-x-8">
            <a href="{{ route('dashboard') }}" class="text font-medium text-gray-700 hover:text-gray-900">Dashboard</a>
            <a href="{{ route('customers.index') }}" class="text font-medium text-gray-700 hover:text-gray-900">Customers</a>
            <a href="{{ route('features.index') }}" class="text font-medium text-gray-700 hover:text-gray-900">Features</a>
            <a href="{{ route('releases.index') }}" class="text font-medium text-gray-700 hover:text-gray-900">Releases</a>
            <a href="{{ route('epics.index') }}" class="text font-medium text-gray-700 hover:text-gray-900">Epics</a>
            <a href="{{ route('kanban') }}" class="text font-medium text-gray-700 hover:text-gray-900">Kanban</a>
        </nav>

        <!-- User Dropdown -->
        <div class="relative">
            <button id="user-menu-button" class="flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-200">
                <span>{{ Auth::user()->name }}</span>
                <svg class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-10">
                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
