<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Issue;
use App\Models\Customer;
use App\Services\JiraService;

class IssueCustomers extends Component
{
    public $issue;
    public $selectedCustomerIds = [];
    public $saved = false;

    public function mount(Issue $issue)
    {
        $this->issue = $issue;
        $this->selectedCustomerIds = $issue->customers->pluck('id')->toArray();
    }

    public function syncCustomers()
    {
        $this->issue->customers()->sync($this->selectedCustomerIds);
        app(JiraService::class)->updateIssueCustomers($this->issue);

        $this->saved = true;

        // Optional: reset the flag after a few seconds
        $this->dispatch('customer-updated');
    }

    public function render()
    {
        return view('livewire.issue-customers', [
            'allCustomers' => Customer::orderBy('name')->get(),
        ]);
    }
}
