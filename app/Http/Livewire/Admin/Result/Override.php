<?php

namespace App\Http\Livewire\Admin\Result;

use LivewireUI\Modal\ModalComponent;
use App\Models\Fixture;
use App\Models\Result;

class Override extends ModalComponent
{
    public Fixture $fixture;
    public $homeScore;
    public $awayScore;
    public $totalScore;

    public function mount($fixture_id)
    {
        $this->fixture = Fixture::find($fixture_id);
    }

    public function updated($field)
    {
        $this->totalScore = $this->homeScore + $this->awayScore;
    }

    public function override()
    {
        Result::create([
            'fixture_id' => $this->fixture->id,
            'home_team_id' => $this->fixture->homeTeam->id,
            'home_team_name' => $this->fixture->homeTeam->name,
            'home_score' => $this->homeScore,
            'away_team_id' => $this->fixture->awayTeam->id,
            'away_team_name' => $this->fixture->awayTeam->name,
            'away_score' => $this->awayScore,
            'is_overridden' => true,
            'submitted_by' => auth()->user()->id,
        ]);

        $this->closeModal();

        return redirect()->route('admin.fixtures.show', $this->fixture);
    }

    public function render()
    {
        return view('livewire.admin.result.override');
    }
}
