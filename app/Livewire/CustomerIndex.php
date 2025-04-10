<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class CustomerIndex extends Component
{
    public $search = '';

    public function render()
    {
        $customers = Customer::with('issues')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get();

        return view('livewire.customer-index', [
            'customers' => $customers
        ]);
    }
}
