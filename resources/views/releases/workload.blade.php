<!-- resources/views/releases/workload.blade.php -->

<x-app-layout>
    <div class="container mx-auto py-6">
        <x-slot name="header">
            <h1 class="text-xl font-semibold text-gray-900">
                Epics by Project Manager for Release: {{ $release['name'] }}
            </h1>
        </x-slot>

        @foreach($groupedEpics as $manager => $epics)
            <div class="my-6 py-6 px-8 rounded bg-white">
                <h2 class="text-lg font-bold mb-2">{{ $manager }}</h2>

                {{-- Compute chart data for this manager --}}
                @php
                    $statusCounts = [];
                    $priorityCounts = [];
                    $sizeCounts = [];
                    foreach ($epics as $epic) {
                        // Use fallback values if the data is missing
                        $status = $epic['fields']['status']['name'] ?? 'Unknown';
                        $priority = $epic['fields']['priority']['name'] ?? 'Unknown';
                        $size = $epic['fields']['customfield_10507']['value'] ?? 'Unknown';

                        $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                        $priorityCounts[$priority] = ($priorityCounts[$priority] ?? 0) + 1;
                        $sizeCounts[$size] = ($sizeCounts[$size] ?? 0) + 1;
                    }
                    // Reorder size counts to follow specific order: XL, L, M, S, Unknown
                    $sizeOrder = ['XL', 'L', 'M', 'S', 'Unknown'];
                    $orderedSizeCounts = [];
                    foreach ($sizeOrder as $sizeKey) {
                         $orderedSizeCounts[$sizeKey] = $sizeCounts[$sizeKey] ?? 0;
                    }

                    // Generate unique IDs using md5 to avoid spaces and special characters
                    $statusChartId = 'statusChart-' . md5($manager);
                    $priorityChartId = 'priorityChart-' . md5($manager);
                    $sizeChartId = 'sizeChart-' . md5($manager);
                @endphp

                {{-- Chart canvases --}}
                <div class="flex flex-nowrap gap-16 mb-8">
                    <div class="w-1/3">
                        <canvas id="{{ $statusChartId }}"></canvas>
                    </div>
                    <div class="w-1/3">
                        <canvas id="{{ $priorityChartId }}"></canvas>
                    </div>
                    <div class="w-1/3">
                        <canvas id="{{ $sizeChartId }}"></canvas>
                    </div>
                </div>

                {{-- The table for epics --}}
                <table class="datatable min-w-full bg-white shadow rounded">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600">
                            <th class="border px-4 py-2">Key</th>
                            <th class="border px-4 py-2">Epic Title</th>
                            <th class="border px-4 py-2">Size</th>
                            <th class="border px-4 py-2">Status</th>
                            <th class="border px-4 py-2">Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($epics as $epic)
                            <tr>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('epics.show', $epic['key']) }}"
                                       class="text-brand-600 font-medium hover:underline">
                                        {{ $epic['key'] }}
                                    </a>
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $epic['fields']['summary'] ?? 'No Title' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $epic['fields']['customfield_10507']['value'] ?? '--' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $epic['fields']['status']['name'] ?? '--' }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ $epic['fields']['priority']['name'] ?? '--' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Inline script to initialize the charts for this manager --}}
                <script>
                    (function() {
                        // Status Chart
                        var ctxStatus = document.getElementById('{{ $statusChartId }}').getContext('2d');
                        new Chart(ctxStatus, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode(array_keys($statusCounts)) !!},
                                datasets: [{
                                    data: {!! json_encode(array_values($statusCounts)) !!},
                                    backgroundColor: ['#0a9396', '#6d597a', '#355070', '#e56b6f', '#778da9', '#FF9F40']
                                }]
                            },
                            options: {
                                title: {
                                    display: true,
                                    text: 'Epics by Status'
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }

                            }
                        });

                        // Priority Chart
                        var ctxPriority = document.getElementById('{{ $priorityChartId }}').getContext('2d');
                        new Chart(ctxPriority, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode(array_keys($priorityCounts)) !!},
                                datasets: [{
                                    data: {!! json_encode(array_values($priorityCounts)) !!},
                                    backgroundColor: ['#e76f51', '#f4a261', '#e9c46a', '#2a9d8f', '#264653', '#1b263b']
                                }]
                            },
                            options: {
                                title: {
                                    display: true,
                                    text: 'Epics by Priority'
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });

                        // Size Chart
                        var ctxSize = document.getElementById('{{ $sizeChartId }}').getContext('2d');
                        new Chart(ctxSize, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode(array_keys($orderedSizeCounts)) !!},
                                datasets: [{
                                    data: {!! json_encode(array_values($orderedSizeCounts)) !!},
                                    backgroundColor: ['#e76f51', '#f4a261', '#e9c46a', '#2a9d8f', '#264653', '#1b263b']
                                }]
                            },
                            options: {
                                title: {
                                    display: true,
                                    text: 'Epics by Size'
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    })();
                </script>
            </div>
        @endforeach
    </div>

    <!-- DataTable Script -->
    <script>
        $(document).ready(function () {
            $('.datatable').DataTable({
                paging: true,
                pageLength: 50,
                searching: true,
                order: [[4, 'asc']], // Default sort by status
            });
        });
    </script>
</x-app-layout>
