<?php

namespace App\Http\Controllers;

use App\Services\JiraService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $jira;

    public function __construct(JiraService $jira)
    {
        $this->jira = $jira;
    }

    public function index()
    {
        // Fetch all customers
        $customers = $this->jira->getAllCustomersWithDetails();

        // Sort customers by name alphabetically
        $customers = collect($customers)->sortBy('name')->toArray();

        return view('customers.index', compact('customers'));
    }

    public function show($customerId)
    {
        $data = $this->jira->getCustomerDetails($customerId);

        // Sort fix versions by release date
        $groupedItems = $data['groupedItems'];
        $fixVersionDates = $data['fixVersionDates'];

        uksort($groupedItems, function ($a, $b) use ($fixVersionDates) {
            return strtotime($fixVersionDates[$a] ?? '9999-12-31') <=> strtotime($fixVersionDates[$b] ?? '9999-12-31');
        });

        return view('customers.show', [
            'customer' => $data['customer'],
            'counts' => $data['counts'],
            'groupedItems' => $groupedItems,
            'unassignedItems' => $data['unassignedItems'],
        ]);
    }
}
