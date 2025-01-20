<x-app-layout>
    <div x-data="{ search: '' }" class="h-[calc(100vh-76px)] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border shadow-lg">
            <h1 class="text-xl font-semibold text-gray-900">Kanban Board</h1>
            <input
                type="text"
                x-model="search"
                placeholder="Search Epics..."
                class="w-64 px-3 py-1.5 text-sm border border-gray-300 rounded"
            />
        </div>

        <!-- Kanban Board -->
        <div class="flex-grow overflow-hidden bg-repeat bg-center" style="background-image: url('/img/topographic-bg.png');">
            <div class="flex overflow-x-auto h-full">
                @foreach($kanbanData as $column)
                    <div class="flex flex-col flex-shrink-0 sm:w-72 md:w-80 xl:w-96 2xl:w-[420px] h-full">
                        <!-- Column Header -->
                        <div class="flex items-center p-4">
                            <span class="text-sm font-semibold">{{ $column['release']['name'] }}</span>
                            <span class="ml-2 text-sm font-semibold text-brand-500 bg-white rounded px-2 py-0.5">
                                {{ count($column['epics']) }}
                            </span>
                        </div>

                        <!-- Cards -->
                        <div class="flex-grow overflow-y-auto px-4 pb-6">
                            @foreach($column['epics'] as $epic)
                                <a href="/epics/{{ $epic['key'] }}"
                                    x-show="'{{ strtolower($epic['fields']['summary']) }}'.includes(search.toLowerCase()) ||
                                            '{{ strtolower($epic['key']) }}'.includes(search.toLowerCase())"
                                    class="relative flex flex-col items-start p-4 mb-2 bg-white rounded-lg shadow cursor-pointer bg-opacity-90 group hover:bg-opacity-100"
                                >
                                    <!-- Customer Commitment Marker -->
                                    @php
                                        $commitment = $epic['fields']['customfield_10473']['value'] ?? 'None';
                                        $commitmentColor = match ($commitment) {
                                            'Critical' => 'bg-red-500',
                                            'Standard' => 'bg-blue-500',
                                            'Enhancement' => 'bg-brand-500',
                                            default => 'bg-gray-200',
                                        };
                                    @endphp
                                    <div
                                        class="flex items-center justify-center w-8 h-1 mb-2 rounded-full {{ $commitmentColor }}"
                                        title="Customer Commitment: {{ $commitment }}"
                                    >
                                        <span class="sr-only">Customer Commitment: {{ $commitment }}</span>
                                    </div>

                                    <!-- Epic Summary -->
                                    <h4 class="sm:text-sm xl:text-base font-medium">{{ $epic['fields']['summary'] }}</h4>

                                    <!-- Epic Details -->
                                    <div class="flex items-center w-full mt-3 text-xs font-medium text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Pro 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2025 Fonticons, Inc.--><path d="M197.5 32c17 0 33.3 6.7 45.3 18.7l176 176c25 25 25 65.5 0 90.5L285.3 450.7c-25 25-65.5 25-90.5 0l-176-176C6.7 262.7 0 246.5 0 229.5L0 80C0 53.5 21.5 32 48 32l149.5 0zM48 229.5c0 4.2 1.7 8.3 4.7 11.3l176 176c6.2 6.2 16.4 6.2 22.6 0L384.8 283.3c6.2-6.2 6.2-16.4 0-22.6l-176-176c-3-3-7.1-4.7-11.3-4.7L48 80l0 149.5zM112 112a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/></svg>
                                            <span class="ml-1 leading-none">{{ $epic['fields']['priority']['name'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div class="flex-shrink-0 w-6"></div>
            </div>
        </div>
    </div>
</x-app-layout>
