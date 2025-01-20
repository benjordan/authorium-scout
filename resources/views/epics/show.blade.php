<x-app-layout>
    <div class="container mx-auto py-6 space-y-8">
        <!-- Epic Header -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">{{ $epic['fields']['summary'] }}</h1>
                <a href="https://cityinnovate.atlassian.net/browse/{{ $epic['key'] }}"
                   target="_blank"
                   class="text-brand-600 hover:underline text-sm font-medium">
                    View in Jira
                </a>
            </div>
            @if (!empty($epic['fields']['description']))
                <div class="mt-4 text-content">
                    {!! $epic['renderedFields']['description'] ?? 'No description available' !!}
                </div>
            @else
                <p class="mt-4 text-gray-600 italic">No description available for this epic.</p>
            @endif
        </div>

        <!-- Child Issues -->
        <div>
            <h2 class="text-2xl font-bold mb-4">Child Issues</h2>
            @if (!empty($childIssues))
                <div class="bg-white p-6 rounded shadow">
                    <table class="table-auto w-full border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600">
                                <th class="border px-4 py-2 text-left text-sm">Summary</th>
                                <th class="border px-4 py-2 text-left text-sm">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($childIssues as $issue)
                                <tr class="odd:bg-gray-50 even:bg-white">
                                    <td class="border px-4 py-2 font-medium">
                                        <a href="https://cityinnovate.atlassian.net/browse/{{ $issue['key'] }}"
                                           class="text-brand-600 hover:underline"
                                           target="_blank">
                                            {{ $issue['fields']['summary'] ?? 'No summary available' }}
                                        </a>
                                    </td>
                                    <td class="border px-4 py-2 text-sm">
                                        <span class="inline-block px-2 py-1 rounded text-sm font-medium
                                            {{ $issue['fields']['status']['statusCategory']['name'] === 'Done' ? 'bg-brand-100 text-brand-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $issue['fields']['status']['name'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600">No child issues found for this epic.</p>
            @endif
        </div>
    </div>
</x-app-layout>
