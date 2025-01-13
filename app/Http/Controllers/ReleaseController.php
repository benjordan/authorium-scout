<?php

namespace App\Http\Controllers;

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
    public function unreleasedReleases()
    {
        $releases = $this->jira->getUnreleasedReleases();
        return view('releases.index', compact('releases'));
    }

    // Show Critical and P0 epics in a release
    public function criticalEpics($releaseKey)
    {
        // Fetch Critical and P0 epics for the release
        $epics = $this->jira->getAllEpicsInRelease($releaseKey);

        // Define the priority order
        $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];

        // Sort epics by priority and then by summary
        usort($epics, function ($a, $b) use ($priorityOrder) {
            $aPriority = array_search($a['fields']['priority']['name'] ?? '', $priorityOrder);
            $bPriority = array_search($b['fields']['priority']['name'] ?? '', $priorityOrder);

            // If priorities are the same, sort alphabetically by summary
            if ($aPriority === $bPriority) {
                return strcmp($a['fields']['summary'] ?? '', $b['fields']['summary'] ?? '');
            }

            // Otherwise, sort by priority
            return $aPriority - $bPriority;
        });

        // Pass $epics and $releaseKey to the view
        return view('releases.epics', compact('epics', 'releaseKey'));
    }

    // Show epic details with child issues
    public function epicDetails($epicKey)
    {
        $epic = $this->jira->getEpic($epicKey);
        $childIssues = $this->jira->getChildIssues($epicKey);
        return view('releases.epic-details', compact('epic', 'childIssues'));
    }
}
