<?php

namespace App\Http\Livewire\Result;

use Livewire\Component;
use App\Models\Result;

class Show extends Component
{
    public Result $result;

    public function mount(Result $result)
    {
        $this->result = $result;
    }

    public function render()
    {
        return view('livewire.result.show')->layout('layouts.app');
    }
}
