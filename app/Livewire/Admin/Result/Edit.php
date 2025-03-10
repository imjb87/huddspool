<?php

namespace App\Livewire\Admin\Result;

use Livewire\Component;
use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Support\Collection;

class Edit extends Component
{
    public Result $result;
    public Fixture $fixture;
    public array $frames = [];
    public Collection $homePlayers;
    public Collection $awayPlayers;
    public $homeScore = 0;
    public $awayScore = 0;
    public $is_overridden = false;
    public $totalScore = 0;

    public function mount(Result $result)
    {
        $this->result = $result;
        $this->fixture = $result->fixture;

        $this->frames = $result->frames->toArray();
        $this->updatedFrames();
    }

    public function updatedFrames()
    {
        $this->homeScore = array_sum(array_column($this->frames, 'home_score'));
        $this->awayScore = array_sum(array_column($this->frames, 'away_score'));
        $this->totalScore = $this->homeScore + $this->awayScore;
    }

    public function save()
    {
        $this->result->update([
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
        ]);

        $this->result->frames()->delete();

        foreach ($this->frames as $frame) {
            if (!$frame['home_player_id'] && !$frame['away_player_id']) {
                continue;
            }

            $this->result->frames()->create([
                'home_player_id' => $frame['home_player_id'],
                'away_player_id' => $frame['away_player_id'],
                'home_score' => $frame['home_score'],
                'away_score' => $frame['away_score'],
            ]);
        }

        return redirect()->route('admin.fixtures.show', $this->fixture);
    }

    public function render()
    {
        return view('livewire.admin.result.edit')->layout('layouts.admin');
    }
}
