<x-app-layout>
    <div id="kanban-app" class="flex flex-col h-screen">
        <!-- Header -->
        <div class="flex items-center justify-between px-10 py-4 bg-gray-100 shadow">
            <h1 class="text-2xl font-bold">Kanban Board</h1>
            <input
                v-model="search"
                placeholder="Search Epics..."
                class="w-64 p-2 border border-gray-300 rounded"
            />
        </div>

        <!-- Kanban Columns -->
        <div class="flex flex-grow overflow-x-auto">
            <div
                v-for="column in filteredKanbanData"
                :key="column.release.id"
                class="flex flex-col flex-shrink-0 w-72 h-full bg-gray-100 border-r"
            >
                <!-- Column Header -->
                <div class="flex items-center h-12 px-4 bg-white border-b shadow">
                    <span class="text-sm font-semibold">{{ column.release.name }}</span>
                    <span class="ml-2 text-sm font-semibold text-indigo-500 bg-white rounded px-2 py-0.5">
                        {{ column.epics.length }}
                    </span>
                </div>

                <!-- Cards -->
                <div class="flex-grow overflow-y-auto p-4">
                    <div
                        v-for="epic in column.epics"
                        :key="epic.key"
                        class="relative flex flex-col items-start p-4 mb-4 bg-white rounded-lg shadow cursor-pointer bg-opacity-90 group hover:bg-opacity-100"
                    >
                        <a :href="'/epics/' + epic.key">
                            <span
                                class="flex items-center h-6 px-3 text-xs font-semibold text-indigo-500 bg-indigo-100 rounded-full"
                            >
                                {{ epic.fields.priority?.name || 'None' }}
                            </span>
                            <h4 class="mt-3 text-sm font-medium">{{ epic.fields.summary }}</h4>
                        </a>
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
                                <span class="ml-1 leading-none">{{ epic.fields.status.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script id="kanban-data" type="application/json">
        {!! json_encode($kanbanData) !!}
    </script>
    @vite('resources/js/kanban.js')
</x-app-layout>
