<x-layout>
    <h1 class="text-3xl font-bold mb-6">Kanban Board: Unreleased Releases</h1>

    <div x-data="{ search: '' }">
        <!-- Search Input -->
        <input
            type="text"
            x-model="search"
            placeholder="Search Epics..."
            class="w-full p-2 border border-gray-300 rounded mb-4"
        />

        <div id="kanban-board" class="flex overflow-x-scroll space-x-6">
            @foreach($kanbanData as $column)
                <div
                    class="kanban-column min-w-[300px] bg-gray-100 p-4 shadow rounded"
                    x-data="{ epics: {{ json_encode($column['epics']) }} }"
                >
                    <h2 class="text-xl font-bold mb-4">{{ $column['release']['name'] }}</h2>
                    <div class="space-y-4">
                        <template
                            x-for="epic in epics.filter(epic =>
                                epic.fields.summary.toLowerCase().includes(search.toLowerCase()) ||
                                epic.key.toLowerCase().includes(search.toLowerCase())
                            )"
                            :key="epic.key"
                        >
                            <div class="kanban-card p-4 bg-white shadow rounded border hover:bg-gray-100 transition">
                                <a :href="'/epics/' + epic.key">
                                    <h3 class="font-bold text-lg" x-text="epic.fields.summary"></h3>
                                    <p class="text-sm text-gray-500" x-text="'Key: ' + epic.key"></p>
                                    <p class="text-sm text-gray-500" x-text="'Priority: ' + epic.fields.priority.name"></p>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>
