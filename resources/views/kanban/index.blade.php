<x-app-layout>
    <div x-data="{ search: '' }" class="h-screen flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-10 py-4 bg-gray-100 shadow">
            <h1 class="text-2xl font-bold">Kanban Board</h1>
            <input
                type="text"
                x-model="search"
                placeholder="Search Epics..."
                class="w-64 p-2 border border-gray-300 rounded"
            />
        </div>

        <!-- Kanban Board -->
        <div class="flex-grow overflow-hidden">
            <div class="flex overflow-x-auto h-full">
                @foreach($kanbanData as $column)
                    <div class="flex flex-col flex-shrink-0 w-72 h-full bg-gray-100">
                        <!-- Column Header -->
                        <div class="flex items-center p-4">
                            <span class="text-sm font-semibold">{{ $column['release']['name'] }}</span>
                            <span class="ml-2 text-sm font-semibold text-indigo-500 bg-white rounded px-2 py-0.5">
                                {{ count($column['epics']) }}
                            </span>
                        </div>

                        <!-- Cards -->
                        <div class="flex-grow overflow-y-auto p-4">
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
                                            'Enhancement' => 'bg-green-500',
                                            default => 'bg-gray-300',
                                        };
                                    @endphp
                                    <div
                                        class="flex items-center justify-center w-8 h-1 mb-2 rounded-full {{ $commitmentColor }}"
                                        title="Customer Commitment: {{ $commitment }}"
                                    >
                                        <span class="sr-only">Customer Commitment: {{ $commitment }}</span>
                                    </div>

                                    <!-- Epic Summary -->
                                    <h4 class="text-sm font-medium">{{ $epic['fields']['summary'] }}</h4>

                                    <!-- Epic Details -->
                                    <div class="flex items-center w-full mt-3 text-xs font-medium text-gray-400">
                                        <div class="flex items-center">
                                            <svg
                                                class="w-4 h-4 text-gray-300 fill-current"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
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
