<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JiraService;
use App\Models\FixVersion;
use App\Models\Issue;

class EpicController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        $priorityOrder = ['Critical', 'P0', 'P1', 'P2'];

        $epics = Issue::where('type', 'Epic')->get()
            ->sort(function ($a, $b) use ($priorityOrder) {
                $aPriority = array_search($a->priority, $priorityOrder) ?? 99;
                $bPriority = array_search($b->priority, $priorityOrder) ?? 99;

                if ($aPriority === $bPriority) {
                    return strcmp($a->summary, $b->summary);
                }

                return $aPriority - $bPriority;
            });

        return view('epics.index', compact('epics'));
    }

    public function show(Issue $issue)
    {
        return view('epics.show', ['epic' => $issue]);
    }
}
