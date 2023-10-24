<?php

namespace App\Http\Livewire\Admin\Section;

use LivewireUI\Modal\ModalComponent;
use App\Models\Section;
use App\Models\Team;

class Deduct extends ModalComponent
{
    public Section $section;
    public Team $team;
    public $deducted = 0;

    public function mount($section_id, $team_id)
    {
        $this->section = Section::find($section_id);
        $this->team = Team::find($team_id);
        $this->deducted = $this->section->teams()->where('team_id', $this->team->id)->first()->pivot->deducted;
    }

    public function deduct()
    {
        $this->section->teams()->updateExistingPivot($this->team->id, ['deducted' => $this->deducted]);

        $this->emit('refreshSection');

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.admin.section.deduct');
    }
}
