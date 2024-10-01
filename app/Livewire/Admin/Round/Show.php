<?php

namespace App\Livewire\Admin\Round;

use Livewire\Component;
use App\Models\Round;

class Show extends Component
{
    public Round $round;

    public function delete()
    {
        $this->round->delete();

        return redirect()->route('admin.knockouts.show', $this->round->knockout);
    }

    public function render()
    {
        return view('livewire.admin.round.show')->layout('layouts.admin');
    }
}
