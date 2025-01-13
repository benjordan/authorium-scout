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
