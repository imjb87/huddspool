<?php

namespace App\Http\Livewire\Admin\Season;

use Livewire\Component;
use App\Models\Season;

class Show extends Component
{
    public Season $season;

    public function delete()
    {
        $this->season->delete();

        return redirect()->route('admin.seasons.index');
    }

    public function render()
    {
        return view('livewire.admin.season.show')->layout('layouts.admin');
    }
}
