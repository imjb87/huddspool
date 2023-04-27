<?php

namespace App\Http\Livewire\Admin\Season;

use Livewire\Component;
use App\Models\Season;
use App\Rules\DatesAreOrdered;
use App\Rules\OneSeasonOpen;

class Edit extends Component
{
    public Season $season;
    
    protected function rules() { 
        return [
            'season.name' => 'required|string',
            'season.is_open' => ['required', 'boolean', new OneSeasonOpen($this->season)],
            'season.dates' => ['required', 'array', 'size:18', new DatesAreOrdered],
        ];
    }

    protected $messages = [
        'season.name.required' => 'The season name is required',
        'season.dates.required' => 'The dates are required',
    ];

    public function save()
    {
        $this->validate();

        $this->season->save();

        return redirect()->route('admin.seasons.show', $this->season);
    }

    public function render()
    {
        return view('livewire.admin.season.edit')->layout('layouts.admin');
    }
}
