<?php

namespace App\Http\Livewire\Result;

use Livewire\Component;
use App\Models\Result;

class Show extends Component
{
    public Result $result;
    public array $ratings;

    public function mount(Result $result)
    {
        $this->result = $result;
    }

    public function render()
    {
        return view('livewire.result.show')
            ->layout('layouts.app', ['title' => $this->result->fixture->homeTeam->name . ' vs ' . $this->result->fixture->awayTeam->name]);
    }
}
