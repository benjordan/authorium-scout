<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JiraService;

class EpicController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        // Fetch all epics
        $epics = $this->jira->getOpenEpics();

        // Sort epics by priority first, then by summary
        usort($epics, function ($a, $b) {
            $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];
            $aPriority = array_search($a['fields']['priority']['name'], $priorityOrder);
            $bPriority = array_search($b['fields']['priority']['name'], $priorityOrder);

            if ($aPriority === $bPriority) {
                return strcmp($a['fields']['summary'], $b['fields']['summary']);
            }

            return $aPriority - $bPriority;
        });

        return view('epics.index', compact('epics'));
    }

    public function show($epicKey)
    {
        // Get epic details
        $epic = $this->jira->getEpicByKey($epicKey);
        $childIssues = $this->jira->getChildIssues($epicKey);
        return view('epics.show', compact('epic', 'childIssues'));
    }
}
