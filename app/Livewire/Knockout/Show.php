<?php

namespace App\Livewire\Knockout;

use Livewire\Component;
use App\Models\Knockout;

class Show extends Component
{
    public $knockout;

    public function mount(Knockout $knockout)
    {
        $this->knockout = $knockout;
    }

    public function render()
    {
        return view('livewire.knockout.show')->layout('layouts.app', ['title' => $this->knockout->name]);
    }
}
