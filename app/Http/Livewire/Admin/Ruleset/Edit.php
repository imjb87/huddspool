<?php

namespace App\Http\Livewire\Admin\Ruleset;

use Livewire\Component;
use App\Models\Ruleset;

class Edit extends Component
{
    public Ruleset $ruleset;

    protected $rules = [
        'ruleset.name' => 'required|string',
        'ruleset.content' => 'required|string',
    ];

    protected $messages = [
        'ruleset.name.required' => 'The ruleset name is required',
        'ruleset.content.required' => 'The ruleset content is required',
    ];

    public function save()
    {
        $this->validate();

        $this->ruleset->save();

        return redirect()->route('admin.rulesets.show', $this->ruleset);
    }

    public function render()
    {
        return view('livewire.admin.ruleset.edit')->layout('layouts.admin');
    }
}
