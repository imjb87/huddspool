<?php

namespace App\Http\Livewire\Admin\Fixture;

use Livewire\Component;
use App\Models\Section;
use App\Models\Team;

class Edit extends Component
{
    public Section $section;
    public array $schedule = [];

    protected $listeners = ['refreshCreateFixture' => '$refresh'];

    protected function rules()
    {
    }

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->schedule = $section->fixtures->groupBy('week')->toArray();

        $this->validate();
    }

    public function swapFixture($week, $fixture)
    {
        $this->schedule[$week][$fixture] = [
            'home_team_id' => $this->schedule[$week][$fixture]['away_team_id'],
            'home_team_name' => $this->schedule[$week][$fixture]['away_team_name'],
            'away_team_id' => $this->schedule[$week][$fixture]['home_team_id'],
            'away_team_name' => $this->schedule[$week][$fixture]['home_team_name'],
            'venue_id' => Team::find($this->schedule[$week][$fixture]['away_team_id'])->venue_id ?? null,
            'venue_name' => Team::find($this->schedule[$week][$fixture]['away_team_id'])->venue->name ?? null,
            'ruleset_id' => $this->section->ruleset_id,
            'season_id' => $this->section->season_id,
            'section_id' => $this->section->id,
            'fixture_date' => $this->schedule[$week][$fixture]['fixture_date'],
            'week' => $this->schedule[$week][$fixture]['week'],
        ];

        $returnWeek = $week + 9 > 18 ? $week - 9 : $week + 9;

        $this->schedule[$returnWeek][$fixture] = [
            'home_team_id' => $this->schedule[$returnWeek][$fixture]['away_team_id'],
            'home_team_name' => $this->schedule[$returnWeek][$fixture]['away_team_name'],
            'away_team_id' => $this->schedule[$returnWeek][$fixture]['home_team_id'],
            'away_team_name' => $this->schedule[$returnWeek][$fixture]['home_team_name'],
            'venue_id' => Team::find($this->schedule[$returnWeek][$fixture]['away_team_id'])->venue_id ?? null,
            'venue_name' => Team::find($this->schedule[$returnWeek][$fixture]['away_team_id'])->venue->name ?? null,
            'ruleset_id' => $this->section->ruleset_id,
            'season_id' => $this->section->season_id,
            'section_id' => $this->section->id,
            'fixture_date' => $this->schedule[$returnWeek][$fixture]['fixture_date'],
            'week' => $this->schedule[$returnWeek][$fixture]['week'],
        ];

        $this->validate();

        $this->emitSelf('refreshCreateFixture');
    }

    public function render()
    {
        return view('livewire.admin.fixture.edit')->layout('layouts.admin');
    }
}
