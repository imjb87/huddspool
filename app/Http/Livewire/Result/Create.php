<?php

namespace App\Http\Livewire\Result;

use Livewire\Component;
use App\Models\Fixture;
use App\Models\Result;
use Illuminate\Support\Collection;
use App\Rules\PlayerLimit;
use App\Rules\AllFramesHavePlayers;
use App\Rules\FrameScoresAddUpToTen;
use App\Rules\FrameScoreEqualsOne;
use App\Rules\BothPlayersAwardedIfOneIs;

class Create extends Component
{
    public Fixture $fixture;
    public array $frames = [];
    public Collection $homePlayers;
    public Collection $awayPlayers;
    public $homeScore = 0;
    public $awayScore = 0;

    protected function rules()
    {
        return [
            'frames' => ['required', 'array', 'size:10', new PlayerLimit($this->frames), new AllFramesHavePlayers($this->frames), new FrameScoresAddUpToTen($this->frames), new FrameScoreEqualsOne($this->frames), new BothPlayersAwardedIfOneIs($this->frames)]
        ];
    }

    protected $messages = [
        'frames.size' => 'You must enter all 10 frames'
    ];

    public function mount(Fixture $fixture)
    {
        $this->fixture = $fixture;
    
        if ($this->isHomeOrAwayTeam(1)) {
            abort(404);
        }
    
        $user = auth()->user();

        if (!$user || !$this->isCaptain($user->id)) {
            abort(404);
        }

        if ($fixture->fixture_date->gte(now())) {
            abort(404);
        }
    
        if ($this->fixture->result) {
            abort(404);
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->frames[$i] = [
                'home_player_id' => null,
                'away_player_id' => null,
                'home_score' => 0,
                'away_score' => 0,
            ];
        }        
    }
    
    private function isHomeOrAwayTeam($teamId)
    {
        return $this->fixture->homeTeam->id == $teamId || $this->fixture->awayTeam->id == $teamId;
    }
    
    private function isCaptain($userId)
    {
        return $this->fixture->homeTeam?->captain?->id == $userId || $this->fixture->awayTeam?->captain?->id == $userId;
    }    

    public function updatedFrames()
    {
        $this->homeScore = 0;
        $this->awayScore = 0;

        foreach ($this->frames as $key => $frame) {
            if ($frame['home_score'] > 1) {
                $this->frames[$key]['home_score'] = 1;
            }
            if ($frame['home_score'] < 0) {
                $this->frames[$key]['home_score'] = 0;
            }
            if ($frame['away_score'] > 1) {
                $this->frames[$key]['away_score'] = 1;
            }
            if ($frame['away_score'] < 0) {
                $this->frames[$key]['away_score'] = 0;
            }            
            $this->homeScore += (int)$frame['home_score'];
            $this->awayScore += (int)$frame['away_score'];
        }
    }
    
    public function save()
    {
        $this->validate();

        $result = Result::create([
            'fixture_id' => $this->fixture->id,
            'home_team_id' => $this->fixture->homeTeam->id,
            'home_team_name' => $this->fixture->homeTeam->name,
            'home_score' => $this->homeScore,
            'home_deducted' => 0,
            'away_team_id' => $this->fixture->awayTeam->id,
            'away_team_name' => $this->fixture->awayTeam->name,
            'away_score' => $this->awayScore,
            'away_deducted' => 0,
            'is_confirmed' => 1,
            'is_overridden' => 0,
        ]);

        foreach ($this->frames as $frame) {
            $result->frames()->create([
                'home_player_id' => $frame['home_player_id'],
                'away_player_id' => $frame['away_player_id'],
                'home_score' => $frame['home_score'],
                'away_score' => $frame['away_score'],
            ]);
        }

        return redirect()->route('fixture.show', $this->fixture);
    }    

    public function render()
    {
        return view('livewire.result.create')->layout('layouts.app');
    }
}
