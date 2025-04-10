<?php

namespace App\Livewire;

use App\Models\Feature;
use Livewire\Component;

class FeatureIndex extends Component
{
    public $search = '';

    public function render()
    {
        $features = Feature::with('issues')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get();

        return view('livewire.feature-index', [
            'features' => $features
        ]);
    }
}
