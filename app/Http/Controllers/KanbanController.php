<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JiraService;

class KanbanController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        // Get all unreleased releases
        $releases = $this->jira->getUnreleasedReleases();

        // Sort releases by release date
        usort($releases, function ($a, $b) {
            return strtotime($a['releaseDate']) <=> strtotime($b['releaseDate']);
        });

        // Get epics for each release, ordered by priority and then summary
        $kanbanData = [];
        foreach ($releases as $release) {
            $epics = $this->jira->getEpicsInRelease($release['id']);

            // Sort epics by priority and then alphabetically
            usort($epics, function ($a, $b) {
                $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];
                $aPriority = array_search($a['fields']['priority']['name'], $priorityOrder);
                $bPriority = array_search($b['fields']['priority']['name'], $priorityOrder);

                if ($aPriority === $bPriority) {
                    return strcmp($a['fields']['summary'], $b['fields']['summary']);
                }
                return $aPriority - $bPriority;
            });

            // Include the Customer Commitment field
            foreach ($epics as &$epic) {
                $epic['fields']['customerCommitment'] = $epic['fields']['customfield_10473']['value'] ?? null;
            }

            $kanbanData[] = [
                'release' => $release,
                'epics' => $epics,
            ];
        }

        $type = 'index'; // Differentiator for the view
        return view('kanban.index', compact('kanbanData', 'type'));
    }

    public function full()
    {
        // Get all unreleased releases
        $releases = $this->jira->getUnreleasedReleases();

        // Sort releases by release date
        usort($releases, function ($a, $b) {
            return strtotime($a['releaseDate']) <=> strtotime($b['releaseDate']);
        });

        // Prepare data for Kanban
        $kanbanData = [];
        foreach ($releases as $release) {
            $epics = $this->jira->getAllEpicsInRelease($release['id']); // Fetch all epics

            // Sort epics by priority and then alphabetically by summary
            usort($epics, function ($a, $b) {
                $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];
                $aPriority = array_search($a['fields']['priority']['name'], $priorityOrder);
                $bPriority = array_search($b['fields']['priority']['name'], $priorityOrder);

                if ($aPriority === $bPriority) {
                    return strcmp($a['fields']['summary'], $b['fields']['summary']);
                }

                return $aPriority - $bPriority;
            });

            // Include the Customer Commitment field
            foreach ($epics as &$epic) {
                $epic['fields']['customerCommitment'] = $epic['fields']['customfield_10473']['value'] ?? null;
            }

            // Fetch all issues in the release for status counts
            $issues = $this->jira->getIssuesInRelease($release['id']); // All issues in the release

            // Calculate status distribution
            $statusCounts = [];
            foreach ($issues as $issue) {
                $statusName = $issue['fields']['status']['name'];
                if (!isset($statusCounts[$statusName])) {
                    $statusCounts[$statusName] = 0;
                }
                $statusCounts[$statusName]++;
            }

            // Add release data to Kanban
            $kanbanData[] = [
                'release' => $release,
                'epics' => $epics,
                'statusCounts' => $statusCounts, // Status counts for the pie chart
            ];
        }

        $type = 'full'; // Differentiator for the view
        return view('kanban.index', compact('kanbanData', 'type'));
    }
}
