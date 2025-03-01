<?php

namespace App\Livewire\Admin\Ruleset;

use Livewire\Component;
use App\Models\Ruleset;

class Create extends Component
{
    public $ruleset;

    public function mount()
    {
        $this->ruleset = new Ruleset();
    }

    protected $rules = [
        'ruleset.name' => 'required|string',
    ];

    public function save()
    {
        $this->validate();

        $ruleset = Ruleset::create($this->ruleset);

        return redirect()->route('admin.rulesets.show', $ruleset);
    }

    public function render()
    {
        return view('livewire.admin.ruleset.create')->layout('layouts.admin');
    }
}
