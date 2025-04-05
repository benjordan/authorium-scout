<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\JiraService;

class ReleaseController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    // Show unreleased releases (Homepage)
    public function index()
    {
        $releases = $this->jira->getUnreleasedReleases();

        // Define colors for statuses
        $statusColors = [
            'Missing Information' => 'bg-amber-500',
            'PM Analysis' => 'bg-sky-500',
            'In Design' => 'bg-purple-500',
            'Requirement Writing' => 'bg-sky-500',
            'Icebox' => 'bg-gray-500',
            'Ready for Dev' => 'bg-cyan-500',
            'Pending CR' => 'bg-teal-500',
            'In Development' => 'bg-blue-500',
            'QA Review' => 'bg-yellow-500',
            'In QA' => 'bg-teal-500',
            'Ready for Release' => 'bg-brand-500',
            'Pushed Back' => 'bg-red-500',
            'Incoming' => 'bg-gray-500',
            'Dev QA' => 'bg-teal-500',
            'Blocked' => 'bg-red-500',
            'Unknown' => 'bg-gray-500', // Default for unknown statuses
        ];

        // Process each release
        foreach ($releases as &$release) {
            $epics = $this->jira->getEpicsInRelease($release['id']);
            $issues = $this->jira->getIssuesInRelease($release['id']);

            // Count epic statuses
            $epicStatusCounts = collect($epics)
                ->groupBy(fn($epic) => $epic['fields']['status']['name'] ?? 'Unknown')
                ->map(fn($group) => $group->count())
                ->toArray();

            // Count issue statuses
            $issueStatusCounts = collect($issues)
                ->groupBy(fn($issue) => $issue['fields']['status']['name'] ?? 'Unknown')
                ->map(fn($group) => $group->count())
                ->toArray();

            // Extract unique customers from customfield_10506
            $uniqueCustomers = collect($epics)
                ->flatMap(fn($epic) => $epic['fields']['customfield_10506'] ?? [])
                ->map(fn($customer) => [
                    'name' => $customer['value'] ?? null,
                    'id' => $customer['id'] ?? null,
                ])
                ->filter(fn($customer) => !empty($customer['name']))
                ->unique('id')
                ->values()
                ->toArray();

            // Filter risk-watch epics
            $riskWatchEpics = array_filter(
                $epics,
                fn($epic) =>
                isset($epic['fields']['labels']) &&
                    is_array($epic['fields']['labels']) &&
                    in_array('risk-watch', $epic['fields']['labels'])
            );

            // Add processed data to release
            $release['epics'] = $epics;
            $release['issues'] = $issues;
            $release['epicStatusCounts'] = $epicStatusCounts;
            $release['issueCount'] = count($issues);
            $release['issueStatusCounts'] = $issueStatusCounts;
            $release['uniqueCustomers'] = $uniqueCustomers;
            $release['riskWatchEpics'] = $riskWatchEpics;
        }

        // Sort releases by release date
        usort($releases, function ($a, $b) {
            $dateA = $a['releaseDate'] ?? '9999-12-31'; // Use a future date as fallback
            $dateB = $b['releaseDate'] ?? '9999-12-31';
            return strcmp($dateA, $dateB);
        });

        return view('releases.index', compact('releases', 'statusColors'));
    }

    // Show Critical and P0 epics in a release
    public function show($releaseKey)
    {
        // Fetch release details from Jira
        $release = $this->jira->getReleaseDetails($releaseKey); // Ensure this method fetches the release details, including the name

        // Fetch Critical and P0 epics for the release
        $epics = $this->jira->getAllEpicsInRelease($releaseKey);

        // Define the priority order
        $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];

        // Sort epics by priority and then by summary
        usort($epics, function ($a, $b) use ($priorityOrder) {
            $aPriority = array_search($a['fields']['priority']['name'] ?? '', $priorityOrder);
            $bPriority = array_search($b['fields']['priority']['name'] ?? '', $priorityOrder);

            if ($aPriority === $bPriority) {
                return strcmp($a['fields']['summary'] ?? '', $b['fields']['summary'] ?? '');
            }

            return $aPriority - $bPriority;
        });

        // Pass $epics, $releaseKey, and $releaseName to the view
        return view('releases.show', [
            'epics' => $epics,
            'releaseKey' => $releaseKey,
            'releaseName' => $release['name'] ?? $releaseKey, // Use the release name or fallback to the key if the name is unavailable
        ]);
    }

    public function statusDetails($releaseKey, $type, $status)
    {
        // Fetch release details
        $release = $this->jira->getReleaseDetails($releaseKey);

        if ($type === 'epics') {
            // Fetch all epics in the release
            $items = $this->jira->getEpicsInRelease($releaseKey);
        } elseif ($type === 'issues') {
            // Fetch all issues in the release
            $items = $this->jira->getIssuesInRelease($releaseKey);
        } else {
            abort(404, 'Invalid type.');
        }

        // Filter items by status
        $filteredItems = array_filter($items, function ($item) use ($status) {
            return ($item['fields']['status']['name'] ?? '') === $status;
        });

        // Pass data to the view
        return view('releases.statusDetails', [
            'release' => $release,
            'type' => $type,
            'status' => $status,
            'items' => $filteredItems,
        ]);
    }

    public function workload($releaseKey)
    {
        // Fetch release details from Jira
        $release = $this->jira->getReleaseDetails($releaseKey);

        // Fetch Critical and P0 epics for the release
        $epics = $this->jira->getAllEpicsInRelease($releaseKey);

        // Define the priority order
        $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];

        $groupedEpics = [];
        foreach ($epics as $epic) {
            $manager = $epic['fields']['customfield_10308']['displayName'] ?? 'Unassigned';
            $groupedEpics[$manager][] = $epic;
        }
        // Sort each group by priority using the priorityOrder array
        foreach ($groupedEpics as $manager => &$epicGroup) {
            usort($epicGroup, function ($a, $b) use ($priorityOrder) {
                $aPriority = array_search($a['fields']['priority']['name'] ?? '', $priorityOrder);
                $bPriority = array_search($b['fields']['priority']['name'] ?? '', $priorityOrder);

                // If not found in the array, assign a default value to sort them to the end
                $aPriority = ($aPriority !== false) ? $aPriority : count($priorityOrder);
                $bPriority = ($bPriority !== false) ? $bPriority : count($priorityOrder);

                // If priorities are equal, sort alphabetically by summary
                if ($aPriority === $bPriority) {
                    return strcmp($a['fields']['summary'] ?? '', $b['fields']['summary'] ?? '');
                }
                return $aPriority - $bPriority;
            });
        }
        unset($epicGroup);

        //dd($groupedEpics);

        // Pass the data to the view
        return view('releases.workload', [
            'release'      => $release,
            'groupedEpics' => $groupedEpics,
        ]);
    }
}
