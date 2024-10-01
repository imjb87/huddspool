<?php

namespace App\Livewire\Admin\Knockout;

use Livewire\Component;
use App\Models\Season;
use App\Models\Knockout;

class Create extends Component
{
    public Season $season;
    public $knockout;

    protected function rules()
    {
        return [
            'knockout.name' => 'required|string',
            'knockout.type' => 'required|string|in:singles,doubles,team',
        ];
    }

    protected $messages = [
        'knockout.name.required' => 'The knockout name is required.',
        'knockout.type.required' => 'The knockout type is required.',
    ];

    public function mount(Season $season)
    {
        $this->season = $season;
    }

    public function save()
    {
        $this->validate();

        $knockout = Knockout::create([
            'name' => $this->knockout['name'],
            'type' => $this->knockout['type'],
            'season_id' => $this->season->id,
        ]);

        return redirect()->route('admin.knockouts.show', $knockout);
    }

    public function render()
    {
        return view('livewire.admin.knockout.create')->layout('layouts.admin');
    }
}
