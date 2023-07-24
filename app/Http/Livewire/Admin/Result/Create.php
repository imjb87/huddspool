<?php

namespace App\Http\Livewire\Admin\Result;

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
    public $is_overridden = false;

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

        for ($i = 1; $i <= 10; $i++) {
            $this->frames[$i] = [
                'home_player_id' => null,
                'away_player_id' => null,
                'home_score' => 0,
                'away_score' => 0,
            ];
        }
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
            $this->homeScore += $frame['home_score'];
            $this->awayScore += $frame['away_score'];
        }
    }

    public function save()
    {
        if(!$this->is_overridden) {
            $this->validate();
        }

        $result = Result::create([
            'fixture_id' => $this->fixture->id,
            'home_team_id' => $this->fixture->homeTeam->id,
            'home_team_name' => $this->fixture->homeTeam->name,
            'home_score' => $this->homeScore,
            'away_team_id' => $this->fixture->awayTeam->id,
            'away_team_name' => $this->fixture->awayTeam->name,
            'away_score' => $this->awayScore,
            'is_overridden' => $this->is_overridden,
            'submitted_by' => auth()->user()->id,
        ]);

        if(!$this->is_overridden) {
            foreach ($this->frames as $frame) {
                $result->frames()->create([
                    'home_player_id' => $frame['home_player_id'],
                    'away_player_id' => $frame['away_player_id'],
                    'home_score' => $frame['home_score'],
                    'away_score' => $frame['away_score'],
                ]);
            }
        }

        return redirect()->route('admin.fixtures.show', $this->fixture);
    }

    public function render()
    {
        return view('livewire.admin.result.create')->layout('layouts.admin');
    }
}
