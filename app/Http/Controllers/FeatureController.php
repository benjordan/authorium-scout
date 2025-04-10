<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
use App\Models\Feature;

use Illuminate\Http\Request;

class FeatureController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        $features = Feature::all(); // or add pagination if you enjoy performance
        return view('features.index', compact('features'));
    }

    public function show($componentId)
    {
        $feature = Feature::with(['issues.productManager'])
            ->where('jira_id', $componentId)
            ->firstOrFail();

        // Prepare a sorted list of unreleased fix versions for grouping
        $issues = $feature->issues;

        // Group issues by unreleased fix version name
        $groupedIssues = collect();

        foreach ($issues as $issue) {
            $unreleasedFixes = $issue->fixVersions->filter(function ($fix) {
                return !$fix->released && $fix->release_date;
            });

            if ($unreleasedFixes->isEmpty()) {
                $groupedIssues->put('Unassigned', $groupedIssues->get('Unassigned', collect())->push($issue));
            } else {
                foreach ($unreleasedFixes as $fix) {
                    $groupedIssues->put($fix->name, $groupedIssues->get($fix->name, collect())->push($issue));
                }
            }
        }

        // Sort by fix version release date (with Unassigned at the end)
        $sortedGrouped = $groupedIssues->sortBy(function ($issues, $key) {
            if ($key === 'Unassigned') {
                return now()->addCentury();
            }
            $fix = $issues->first()->fixVersions->firstWhere('name', $key);
            return $fix->release_date ?? now()->addCentury();
        });

        // Count by issue type
        $counts = $feature->issues()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('features.show', [
            'feature' => $feature,
            'groupedIssues' => $sortedGrouped,
            'counts' => $counts,
        ]);
    }
}
