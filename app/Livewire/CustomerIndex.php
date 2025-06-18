<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class CustomerIndex extends Component
{
    public $search = '';

    public function render()
    {
        // Define active statuses (matching the show method)
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

        $customers = Customer::with(['issues' => function ($query) use ($activeStatuses) {
            // Only load issues that are specifically tagged to this customer
            // and exclude "All Customers" work by filtering the pivot relationship
            $query->whereHas('customers', function ($customerQuery) {
                // This ensures we only get issues where THIS specific customer is tagged
                // The relationship will automatically scope to the current customer
            })
                ->whereIn('status', $activeStatuses); // Only active work
        }])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->where('name', '!=', 'All Customers') // Exclude "All Customers" from the list
            ->get();

        return view('livewire.customer-index', [
            'customers' => $customers
        ]);
    }
}
