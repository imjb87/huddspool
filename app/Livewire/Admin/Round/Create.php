<?php

namespace App\Livewire\Admin\Round;

use Livewire\Component;
use App\Models\Knockout;
use App\Models\Round;

class Create extends Component
{
    public Knockout $knockout;
    public $round;

    protected function rules()
    {
        return [
            'round.name' => 'required|string',
            'round.date' => 'required|date',
            'round.best_of' => 'required|integer',
        ];
    }

    protected $messages = [
        'round.name.required' => 'The round name is required.',
        'round.date.required' => 'The round date is required.',
        'round.date.date' => 'The round date must be a valid date.',
        'round.best_of.required' => 'The round best of is required.',
    ];

    public function mount(Knockout $knockout)
    {
        $this->knockout = $knockout;
    }

    public function save()
    {
        $this->validate();

        $round = Round::create([
            'name' => $this->round['name'],
            'date' => $this->round['date'],
            'knockout_id' => $this->knockout->id,
        ]);

        return redirect()->route('admin.rounds.show', $round);
    }

    public function render()
    {
        return view('livewire.admin.round.create')->layout('layouts.admin');
    }
}
