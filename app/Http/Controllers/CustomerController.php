<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\JiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        $customers = customer::all(); // or add pagination if you enjoy performance
        return view('customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::with(['issues.fixVersions'])
            ->where('jira_id', $id)
            ->firstOrFail();

        // Prepare a sorted list of unreleased fix versions for grouping
        $issues = $customer->issues;

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
        $counts = $customer->issues()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('customers.show', [
            'customer' => $customer,
            'groupedIssues' => $sortedGrouped,
            'counts' => $counts,
        ]);
    }
}
