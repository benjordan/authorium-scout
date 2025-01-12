<x-layout>
    <h1 class="text-2xl font-bold">{{ $epic['fields']['summary'] }}</h1>
    <p class="mt-4">{{ is_array($epic['fields']['summary']) ? implode(', ', $epic['fields']['summary']) : $epic['fields']['summary'] }}</p>

    <h2 class="text-xl mt-8">Child Issues</h2>
    <table class="mt-4 table-auto w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">Summary</th>
                <th class="border px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($childIssues as $issue)
                <tr>
                    <td class="border px-4 py-2">{{ $issue['fields']['summary'] }}</td>
                    <td class="border px-4 py-2">{{ $issue['fields']['status']['name'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
