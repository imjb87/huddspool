<?php

namespace App\Livewire\Admin\Round;

use Livewire\Component;
use App\Models\Round;

class Edit extends Component
{
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

    public function mount(Round $round)
    {
        $this->round = $round;
    }

    public function save()
    {
        $this->validate();

        $this->round->save();

        return redirect()->route('admin.rounds.show', $this->round);
    }

    public function render()
    {
        return view('livewire.admin.round.edit')->layout('layouts.admin');
    }
}
