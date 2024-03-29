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
use App\Rules\TotalScoresAddUpToTen;
use App\Models\User;

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

    protected function rules()
    {
        return [
            'frames' => ['required', 'array', 'size:10', new PlayerLimit($this->frames), new AllFramesHavePlayers($this->frames), new FrameScoresAddUpToTen($this->frames), new FrameScoreEqualsOne($this->frames), new BothPlayersAwardedIfOneIs($this->frames)],
            'totalScore' => [new TotalScoresAddUpToTen($this->totalScore)],
        ];
    }

    protected $messages = [
        'frames.size' => 'You must enter all 10 frames'
    ];

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
        $this->validate();

        $this->result->update([
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
        ]);

        $this->result->frames()->delete();

        foreach ($this->frames as $frame) {
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
