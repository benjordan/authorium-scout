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
        return view('releases.index', compact('releases'));
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
}
