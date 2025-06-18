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

        // Only use this specific customer - remove "All Customers" logic
        $customerIds = [$customer->id];

        // Define status category mapping
        $activeStatuses = [
            // "In Progress" category statuses
            'PM Analysis',
            'In Design',
            'Requirement Writing',
            'In Development',
            'Pending CR',
            'Ready for QA',
            'Dev QA',
            'In QA',
            // "To Do" category statuses
            'Incoming',
            'Missing Information',
            'Ready for Dev',
            'Blocked',
            'Pushed Back'
        ];

        // Now load issues for this customer + All Customers (if needed)
        // Make sure to eager load fixVersions relationship
        $issues = \App\Models\Issue::with('fixVersions')
            ->whereHas('customers', function ($query) use ($customerIds) {
                $query->whereIn('customers.id', $customerIds);
            })
            ->whereIn('status', $activeStatuses) // Filter by active status categories
            ->get()
            ->sortBy(function ($issue) {
                // Define priority order: Critical, P0, P1, P2, P3, then everything else
                $priorityOrder = [
                    'Critical' => 1,
                    'P0' => 2,
                    'P1' => 3,
                    'P2' => 4,
                    'P3' => 5,
                ];

                return $priorityOrder[$issue->priority] ?? 999; // Unknown priorities go to the end
            });

        // Group by commitment status
        $committedWork = collect();
        $openWork = collect();

        foreach ($issues as $issue) {
            $commitmentStatus = strtolower($issue->release_commit_status ?? '');

            // Committed work: has "committed" status AND has fix versions
            if ($commitmentStatus === 'committed' && $issue->fixVersions && $issue->fixVersions->count() > 0) {
                $committedWork->push($issue);
            } else {
                // Open work: everything else (placeholder, tentative, none, or no status/fix version)
                $openWork->push($issue);
            }
        }

        $groupedIssues = [
            'committed' => $committedWork,
            'open' => $openWork
        ];

        $counts = \App\Models\Issue::whereHas('customers', function ($query) use ($customerIds) {
            $query->whereIn('customers.id', $customerIds);
        })
            ->whereIn('status', $activeStatuses) // Filter by active status categories
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return view('customers.show', [
            'customer' => $customer,
            'groupedIssues' => $groupedIssues,
            'counts' => $counts,
        ]);
    }
}
