<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Result;
use Livewire\WithPagination;

class LatestResults extends Component
{
    use WithPagination;
    
    public function render()
    {
        return view('livewire.admin.latest-results', 
            [
                'results' => Result::with('fixture')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(5)
            ]
        );
    }
}
