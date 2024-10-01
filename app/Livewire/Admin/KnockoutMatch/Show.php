<?php

namespace App\Livewire\Admin\KnockoutMatch;

use Livewire\Component;
use App\Models\KnockoutMatch;

class Show extends Component
{
    public KnockoutMatch $match;

    public function delete()
    {
        $this->match->delete();

        return redirect()->route('admin.rounds.show', $this->match->round);
    }

    public function render()
    {
        return view('livewire.admin.knockoutmatch.show')->layout('layouts.admin');
    }
}
