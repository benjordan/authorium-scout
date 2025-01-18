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
        $customers = $this->jira->getAllCustomers();

        return view('customers.index', compact('customers'));
    }

    public function show($customerId)
    {
        $data = $this->jira->getCustomerDetails($customerId);

        return view('customers.show', [
            'customer' => $data['customer'],
            'epics' => $data['epics'],
            'bugs' => $data['bugs'],
            'requests' => $data['requests'],
        ]);
    }
}
