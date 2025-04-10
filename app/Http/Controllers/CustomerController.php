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

        // Fetch the "All Customers" record to check against
        $allCustomers = Customer::where('name', 'All Customers')->first();

        $customerIds = [$customer->id];

        // If we're not already looking at "All Customers", include it in the filter
        if ($customer->name !== 'All Customers' && $allCustomers) {
            $customerIds[] = $allCustomers->id;
        }

        // Now load issues for this customer + All Customers (if needed)
        $issues = \App\Models\Issue::with('fixVersions')
            ->whereHas('customers', function ($query) use ($customerIds) {
                $query->whereIn('customers.id', $customerIds);
            })
            ->get();

        // Group by unreleased fix version
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

        $sortedGrouped = $groupedIssues->sortBy(function ($issues, $key) {
            if ($key === 'Unassigned') {
                return now()->addCentury();
            }
            $fix = $issues->first()->fixVersions->firstWhere('name', $key);
            return $fix->release_date ?? now()->addCentury();
        });

        $counts = \App\Models\Issue::whereHas('customers', function ($query) use ($customerIds) {
            $query->whereIn('customers.id', $customerIds);
        })
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
