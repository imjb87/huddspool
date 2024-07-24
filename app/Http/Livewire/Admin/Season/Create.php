<?php

namespace App\Http\Livewire\Admin\Season;

use Livewire\Component;
use App\Models\Season;
use App\Rules\DatesAreOrdered;
use App\Rules\OneSeasonOpen;

class Create extends Component
{
    public $season;

    protected function rules() { 
        return [
            'season.name' => 'required|string',
            'season.dates' => ['required', 'array', 'size:18', new DatesAreOrdered],
        ];
    }

    protected $messages = [
        'season.name.required' => 'The season name is required.',
        'season.dates.required' => 'All weeks must be assigned a date.',
        'season.dates.size' => 'All weeks must be assigned a date.',
    ];

    public function save()
    {
        $this->validate();

        $season = Season::create([
            'name' => $this->season['name'],
            'slug' => \Str::slug($this->season['name']),
            'dates' => $this->season['dates'],
            'is_open' => 0,
        ]);

        return redirect()->route('admin.seasons.show', $season);
    }

    public function render()
    {
        return view('livewire.admin.season.create')->layout('layouts.admin');
    }
}
