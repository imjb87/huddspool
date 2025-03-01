<?php

namespace App\Livewire\Admin\Ruleset;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ruleset;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.ruleset.index', [
            'rulesets' => Ruleset::simplePaginate(10)
        ])->layout('layouts.admin');
    }
}
