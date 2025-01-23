<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
    <div class="container mx-auto py-6 space-y-8">
        <div class="bg-white p-6 rounded shadow flex flex-col lg:flex-row justify-between">
            <!-- Left Column: Epic Summary -->
            <div class="flex-1 lg:w-[55%] mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-800">{{ $epic['fields']['summary'] }}</h1>
                @if (!empty($epic['fields']['description']))
                    <div class="mt-4 text-content">
                        {!! $epic['renderedFields']['description'] ?? 'No description available' !!}
                    </div>
                @else
                    <p class="mt-4 text-gray-600 italic">No description available for this epic.</p>
                @endif
            </div>

            <!-- Right Column: Epic Details -->
            <div class="lg:w-[45%] lg:ml-8">
                <div class="flex justify-between mb-2 items-center">
                    <h2 class="text-lg font-bold text-gray-700 mr-4">Epic Details</h2>
                    <a href="https://cityinnovate.atlassian.net/browse/{{ $epic['key'] }}"
                        target="_blank"
                        class="bg-brand-600 rounded border text-white py-2 px-3 hover:underline text-sm font-semibold">
                            View in Jira
                    </a>
                </div>

                <div class="bg-gray-50 p-4 rounded border">
                    <div class="divide-y divide-gray-200">
                        <!-- Key -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium">Key:</span>
                            <a href="https://cityinnovate.atlassian.net/browse/{{ $epic['key'] }}" target="_blank" class="font-medium underline">{{ $epic['key'] }}</a>
                        </div>
                        <!-- PM Assigned -->
                        @if (!empty($epic['fields']['customfield_10506']))
                            <div class="flex justify-between py-2 items-center">
                                <span class="text-gray-600 font-medium">PM Assigned:</span>
                                <div class="flex items-center">
                                    <img src="{{ $epic['fields']['customfield_10308']['avatarUrls']['48x48'] ?? '' }}"
                                         alt="{{ $epic['fields']['customfield_10308']['displayName'] ?? 'PM Avatar' }}"
                                         class="w-8 h-8 rounded-full mr-2">
                                    <span class="font-semibold">{{ $epic['fields']['customfield_10308']['displayName'] ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        @endif
                        <!-- Status -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium">Status:</span>
                            <span class="font-semibold">{{ $epic['fields']['status']['name'] ?? 'Unknown' }}</span>
                        </div>
                        <!-- Fix Version -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium mr-2">Fix Version:</span>
                            <span class="font-semibold">
                                @foreach ($epic['fields']['fixVersions'] ?? [] as $fixVersion)
                                    <a href="{{ route('releases.show', $fixVersion['id']) }}"
                                    class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-medium hover:underline">
                                        {{ $fixVersion['name'] }}
                                    </a>
                                @endforeach
                            </span>
                        </div>
                        <!-- Customer Commitment -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium mr-2">Customer Commitment:</span>
                            <div>
                                @php
                                    $commitment = $epic['fields']['customfield_10473']['value'] ?? 'None';
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
                        </div>
                        <!-- Customers -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium mr-2">Customers:</span>
                            <span class="font-semibold">
                                @if (!empty($epic['fields']['customfield_10506']))
                                    {!! implode(' ', array_map(function($customer) {
                                        return '<a href="' . route('customers.show', $customer['id']) . '" class="inline-block px-2 py-1 bg-brand-100 text-brand-800 rounded text-sm font-medium hover:underline">' . e($customer['value']) . '</a>';
                                    }, $epic['fields']['customfield_10506'])) !!}
                                @else
                                    <span class="text-gray-500">None</span>
                                @endif
                            </span>
                        </div>
                        <!-- Size -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium mr-2">Size:</span>
                            <span class="font-semibold">{{ $epic['fields']['customfield_10507']['value'] ?? 'Unknown' }}</span>
                        </div>
                        <!-- Components -->
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium mr-2">Components:</span>
                            <span class="font-semibold">
                                @if (!empty($epic['fields']['components']))
                                    {!! implode(', ', array_map(function($component) {
                                        return '<a href="' . e($component['self']) . '" target="_blank" class="inline-block px-2 py-1 bg-blue-50 text-blue-800 rounded text-sm font-medium hover:underline">' . e($component['name']) . '</a>';
                                    }, $epic['fields']['components'])) !!}
                                @else
                                    <span class="text-gray-500">None</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
