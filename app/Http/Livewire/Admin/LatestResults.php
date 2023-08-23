<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Result;

class LatestResults extends Component
{
    public function render()
    {
        return view('livewire.admin.latest-results', 
            [
                'results' => Result::with('fixture')
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(10)
            ]
        );
    }
}
