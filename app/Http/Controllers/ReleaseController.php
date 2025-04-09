<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\JiraService;
use App\Models\FixVersion;


class ReleaseController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    private function sortByStatus($collection, $order)
    {
        return $collection->sortBy(function ($_, $key) use ($order) {
            $index = array_search($key, $order);
            return $index !== false ? $index : PHP_INT_MAX;
        });
    }

    // Show unreleased releases (Homepage)
    public function index()
    {
        $statusColors = [
            'Unknown' => 'bg-gray-500',
            'Incoming' => 'bg-gray-500',
            'Missing Information' => 'bg-amber-500',
            'PM Analysis' => 'bg-sky-500',
            'In Design' => 'bg-purple-500',
            'Requirement Writing' => 'bg-sky-500',
            'Ready for Dev' => 'bg-cyan-500',
            'Pending CR' => 'bg-teal-500',
            'In Development' => 'bg-blue-500',
            'QA Review' => 'bg-yellow-500',
            'In QA' => 'bg-teal-500',
            'Pushed Back' => 'bg-red-500',
            'Ready for Release' => 'bg-brand-500',
            'Ready for QA' => 'bg-brand-500',
            'Linked for Completion' => 'bg-gray-300',
            'Dev QA' => 'bg-teal-500',
            'Icebox' => 'bg-gray-500',
            'Closed' => 'bg-gray-500',
            'Blocked' => 'bg-red-500',
            'Released' => 'bg-brand-500',
        ];

        $statusOrder = array_keys($statusColors);

        $releases = FixVersion::with([
            'issues.customers',
            'issues.components',
        ])->where('released', false)->orderBy('release_date')->get()->map(function ($release) use ($statusOrder) {
            $epics = $release->issues->where('type', 'Epic');
            $tickets = $release->issues;

            return (object) [
                'release' => $release,
                'epics_by_status' => $this->sortByStatus($epics->groupBy('status')->map->count(), $statusOrder),
                'tickets_by_status' => $this->sortByStatus($tickets->groupBy('status')->map->count(), $statusOrder),
                'customers' => $tickets->flatMap->customers->unique('id'),
            ];
        });

        return view('releases.index', compact('releases', 'statusColors'));
    }

    // Show Critical and P0 epics in a release
    public function show($releaseId)
    {
        $release = FixVersion::with(['issues.customers', 'issues.components'])->findOrFail($releaseId);

        return view('releases.show', [
            'release' => $release,
            'releaseName' => $release->name,
            'releaseKey' => $release->jira_id,
        ]);
    }

    public function statusDetails($releaseKey, $type, $status)
    {
        // Fetch release from database
        $release = FixVersion::with(['issues.customers', 'issues.components'])
            ->where('id', $releaseKey)
            ->firstOrFail();

        if ($type === 'epics') {
            // Get issues of type Epic with the given status
            $items = $release->issues
                ->where('type', 'Epic')
                ->where('status', $status);
        } elseif ($type === 'issues') {
            // Get all issues with the given status
            $items = $release->issues
                ->where('status', $status);
        } else {
            abort(404, 'Invalid type.');
        }

        return view('releases.statusDetails', [
            'release' => $release,
            'type' => $type,
            'status' => $status,
            'items' => $items,
        ]);
    }

    public function workload($releaseId)
    {
        $release = FixVersion::with(['issues.productManagers'])->findOrFail($releaseId);

        $epics = $release->issues()
            ->where('type', 'Epic')
            ->with('productManagers')
            ->get();

        // Flatten each epic into one per PM (or 'Unassigned' if none)
        $groupedEpics = collect();

        foreach ($epics as $epic) {
            $managers = $epic->productManagers;
            dd($managers);

            if ($managers->isEmpty()) {
                $groupedEpics['unassigned']['name'] = 'Unassigned';
                $groupedEpics['unassigned']['epics'][] = $epic;
            } else {
                foreach ($managers as $pm) {
                    $key = Str::slug($pm->name);
                    $groupedEpics[$key]['name'] = $pm->name;
                    $groupedEpics[$key]['epics'][] = $epic;
                }
            }
        }

        return view('releases.workload', compact('release', 'groupedEpics'));
    }
}
