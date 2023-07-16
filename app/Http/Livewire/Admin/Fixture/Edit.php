<?php

namespace App\Http\Livewire\Admin\Fixture;

use Livewire\Component;
use App\Models\Section;
use App\Models\Fixture;
use App\Models\Team;
use App\Rules\NoFixtureClashes;

class Edit extends Component
{
    public Section $section;
    public array $schedule = [];

    protected $listeners = ['refreshCreateFixture' => '$refresh'];

    protected function rules()
    {
        return ['schedule' => [new NoFixtureClashes($this->schedule, $this->section->season_id, $this->section->id)]];
    }

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->schedule = $section->fixtures->groupBy('week')->map(function ($week) {
            return $week->map(function ($fixture) {
                return [
                    'home_team_id' => $fixture->home_team_id,
                    'home_team_name' => $fixture->homeTeam->name,
                    'away_team_id' => $fixture->away_team_id,
                    'away_team_name' => $fixture->awayTeam->name,
                    'venue_id' => $fixture->venue_id,
                    'venue_name' => $fixture->venue->name,
                    'ruleset_id' => $fixture->ruleset_id,
                    'season_id' => $fixture->season_id,
                    'section_id' => $fixture->section_id,
                    'fixture_date' => $fixture->fixture_date,
                    'week' => $fixture->week,
                    'id' => $fixture->id,
                ];
            })->toArray();
        })->toArray();

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
            'id' => $this->schedule[$week][$fixture]['id'],
        ];

        Fixture::find($this->schedule[$week][$fixture]['id'])->update([
            'home_team_id' => $this->schedule[$week][$fixture]['home_team_id'],
            'away_team_id' => $this->schedule[$week][$fixture]['away_team_id'],
            'venue_id' => $this->schedule[$week][$fixture]['venue_id'],
        ]);

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
            'id' => $this->schedule[$returnWeek][$fixture]['id'],
        ];

        Fixture::find($this->schedule[$returnWeek][$fixture]['id'])->update([
            'home_team_id' => $this->schedule[$returnWeek][$fixture]['home_team_id'],
            'away_team_id' => $this->schedule[$returnWeek][$fixture]['away_team_id'],
            'venue_id' => $this->schedule[$returnWeek][$fixture]['venue_id'],
        ]);

        $this->validate();

        $this->emitSelf('refreshCreateFixture');
    }

    public function render()
    {
        return view('livewire.admin.fixture.edit')->layout('layouts.admin');
    }
}
