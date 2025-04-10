<x-app-layout>

    <div class="container mx-auto py-6 space-y-2">
        @php
            $status = strtolower($epic->release_commit_status ?? 'unknown');
            $statusMap = [
                'locked' => ['bg-blue-100 bg-opacity-60 text-blue-800', 'Locked', 'This epic has been locked in to deliver for the release it is associated with.'],
                'tentative' => ['bg-orange-100 bg-opacity-60 text-orange-800', 'Tentative', 'This epic is tentatively planned for this release but could very likely be moved out to fulfill other committments.'],
                'confirmed' => ['bg-brand-100 bg-opacity-60 text-brand-800', 'Confirmed', 'This epic has been confirmed by leadership as part of its associated release.'],
                'committed' => ['bg-brand-100 bg-opacity-60 text-brand-800', 'Committed', 'This epic is committed to be delivered in the release it is associated with.'],
            ];
        @endphp

        @if (array_key_exists($status, $statusMap))
            @php
                $statusClass = $statusMap[$status][0];
                $statusTitle = $statusMap[$status][1];
                $statusDescription = $statusMap[$status][2];
            @endphp

            <div class="rounded border px-4 py-3 {{ $statusClass }}">
                <div class="font-bold">{{ $statusTitle }} Epic</div>
                <p class="text-sm">{{ $statusDescription }}</p>
            </div>
        @endif
        <div class="bg-white p-6 rounded shadow flex flex-col lg:flex-row justify-between">
            <!-- Left Column: Epic Summary -->
            <div class="flex-1 lg:w-[55%] mb-4 lg:mb-0">
                <a href="https://cityinnovate.atlassian.net/browse/{{ $epic->jira_key }}" target="_blank" class="inline-block bg-gray-100 px-2 py-1 text-sm mb-2 font-medium rounded">
                    {{ $epic->jira_key }}
                </a>
                @php
                    $type = strtolower($epic->type);
                    $typeStyles = [
                        'epic' => 'bg-purple-100 text-purple-800',
                        'request' => 'bg-orange-100 text-orange-800',
                        'bug' => 'bg-red-100 text-red-800',
                        'story' => 'bg-blue-100 text-blue-800',
                    ];
                    $badgeClass = $typeStyles[$type] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="inline-block px-2 py-1 text-sm mb-2 font-medium rounded {{ $badgeClass }}">
                    {{ ucfirst($epic->type) }}
                </span>
                <h1 class="text-3xl font-bold text-gray-800">{{ $epic->summary }}</h1>
                @if (!empty($epic->description))
                    <div class="mt-4 text-content max-w-none">
                        {!! html_entity_decode($epic->description) !!}
                    </div>
                @else
                    <p class="mt-4 text-gray-600 italic">No description available for this epic.</p>
                @endif
            </div>

            <!-- Right Column: Epic Details -->
            <div class="lg:w-[45%] lg:ml-8">
                <div class="flex justify-between mb-2 items-center">
                    <h2 class="text-lg font-bold text-gray-700 mr-4">Item Details</h2>
                    <a href="https://cityinnovate.atlassian.net/browse/{{ $epic->jira_key }}"
                        target="_blank"
                        class="bg-brand-600 rounded border text-white py-2 px-3 hover:underline text-sm font-semibold">
                        View in Jira
                    </a>
                </div>

                <div class="bg-gray-50 p-4 rounded border divide-y divide-gray-200">

                    <!-- Status -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Status:</span>
                        <span class="font-semibold">{{ $epic->status ?? 'Unknown' }}</span>
                    </div>

                    <!-- Size -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Size:</span>
                        <span class="font-semibold">{{ $epic->size ?? 'Unknown' }}</span>
                    </div>

                    <!-- Size -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Release Commit Status:</span>
                        <span class="font-semibold">{{ $epic->release_commit_status ?? 'Unknown' }}</span>
                    </div>

                    <!-- Priority -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Priority:</span>
                        @php
                            $priority = $epic->priority ?? 'Unknown';
                            $priorityColor = match ($priority) {
                                'Critical' => 'bg-red-100 text-red-800',
                                'P0' => 'bg-orange-100 text-orange-800',
                                'P1' => 'bg-yellow-100 text-yellow-800',
                                'P2' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded {{ $priorityColor }}">
                            {{ $priority }}
                        </span>
                    </div>

                    <!-- Fix Versions -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Fix Versions:</span>
                        <span class="font-semibold space-x-1">
                            @foreach ($epic->fixVersions as $fix)
                                <a href="{{ route('releases.show', $fix->id) }}"
                                    class="inline-block px-2 py-1 bg-blue-50 text-blue-800 rounded text-sm font-medium hover:underline">
                                    {{ $fix->name }}
                                </a>
                            @endforeach
                        </span>
                    </div>

                    <!-- Customer Commitment -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Customer Commitment:</span>
                        @php
                            $commitment = $epic->customer_commitment ?? 'None';
                            $commitmentColor = match ($commitment) {
                                'Critical' => 'bg-red-100 text-red-800',
                                'Standard' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-medium rounded {{ $commitmentColor }}">
                            {{ $commitment }}
                        </span>
                    </div>

                    <!-- Customers -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Customers:</span>
                        <span class="font-semibold flex gap-1 flex-wrap">
                            @forelse ($epic->customers as $customer)
                                <a href="{{ route('customers.show', $customer->id) }}"
                                    class="inline-block px-2 py-1 bg-brand-100 text-brand-800 rounded text-sm font-medium hover:underline">
                                    {{ $customer->name }}
                                </a>
                            @empty
                                <span class="text-gray-500">None</span>
                            @endforelse
                        </span>
                    </div>

                    <!-- Components -->
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600 font-medium">Components:</span>
                        <span class="font-semibold flex gap-1 flex-wrap">
                            @forelse ($epic->components as $component)
                                <span class="inline-block px-2 py-1 bg-blue-50 text-blue-800 rounded text-sm font-medium">
                                    {{ $component->name }}
                                </span>
                            @empty
                                <span class="text-gray-500">None</span>
                            @endforelse
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
