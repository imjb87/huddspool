<?php

namespace App\Http\Livewire\Admin\Section;

use Livewire\Component;
use App\Models\Section;
use App\Models\Fixture;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Section $section;
    public $team_id;

    public $listeners = ['refreshSection' => '$refresh'];

    public function delete()
    {
        $this->section->delete();

        return redirect()->route('admin.seasons.show', $this->section->season);
    }

    public function withdraw($team_id)
    {
        if( $this->team_id != $team_id ) {
            $this->team_id = $team_id;
            return;
        }

        $this->section->teams()->detach($team_id);    
        $this->section->teams()->attach($team_id, ['withdrawn_at' => now()]);

        // determine what week of fixture we are in
        $week = collect($this->section->season->dates)->map(function ($date, $key) {
            if( date('W', strtotime($date)) == date('W') ) {
                return $key + 1;
            } else {
                return 1;
            }
        })->filter()->first();

        if( $week < 9 ) {
            // remove all results for this team in this section
            $this->section->results()->each(function ($result) use ($team_id) {
                if( $result->home_team_id == $team_id || $result->away_team_id == $team_id ) {
                    $result->delete();
                }
            });
        } else {
            // remove all results up to week 9 for this team in this section
            $this->section->results()->where('week', '<', 9)->each(function ($result) use ($team_id) {
                if( $result->home_team_id == $team_id || $result->away_team_id == $team_id ) {
                    $result->delete();
                }
            });
        }

        // set any fixtures that don't have results to byes
        $this->section->fixtures()->each(function ($fixture) use ($team_id) {
            if( $fixture->home_team_id == $team_id ) {
                if( !$fixture->result ) {
                    $fixture->update(['home_team_id' => 1]);
                }
            }
        });

        $this->section->fixtures()->each(function ($fixture) use ($team_id) {
            if( $fixture->away_team_id == $team_id ) {
                if( !$fixture->result ) {
                    $fixture->update(['away_team_id' => 1]);
                }
            }
        });

        $this->team_id = null;
        $this->emit('refreshSection');
    }

    public function showFixture(Fixture $fixture)
    {
        return redirect()->route('admin.fixtures.show', $fixture);
    }

    public function regenerateFixtures()
    {
        $this->section->fixtures()->delete();

        return redirect()->route('admin.fixtures.create', $this->section);
    }

    public function render()
    {
        return view('livewire.admin.section.show', [
            'fixtures' => Fixture::where('section_id', $this->section->id)
                ->orderBy('fixture_date')
                ->simplePaginate(5)
        ])->layout('layouts.admin');
    }
}
