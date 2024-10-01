<?php

namespace App\Livewire\Admin\Knockout;

use Livewire\Component;
use App\Models\Knockout;

class Show extends Component
{
    public Knockout $knockout;

    public function delete()
    {
        $this->knockout->delete();

        return redirect()->route('admin.seasons.show', $this->knockout->season);
    }

    public function render()
    {
        return view('livewire.admin.knockout.show')->layout('layouts.admin');
    }
}
