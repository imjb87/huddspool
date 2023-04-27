<?php

namespace App\Http\Livewire\Admin\Ruleset;

use Livewire\Component;
use App\Models\Ruleset;

class Show extends Component
{
    public Ruleset $ruleset;

    public function delete()
    {
        $this->ruleset->delete();

        return redirect()->route('admin.rulesets.index');
    }

    public function render()
    {
        return view('livewire.admin.ruleset.show')->layout('layouts.admin');
    }
}
