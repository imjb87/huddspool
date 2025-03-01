<?php

namespace App\Livewire\Player;

use Livewire\Component;
use App\Models\User;
use App\Models\Frame;
use Illuminate\Support\Facades\DB;

class Show extends Component
{
    public User $player;
    public $frames = [];
    public $played;
    public $won;
    public $lost;
    public $role;
    public $history = [];

    public function mount(User $player)
    {
        $this->player = $player;
        
        $this->frames = Frame::where(function ($query) {
            $query->where('home_player_id', $this->player->id)
                ->orWhere('away_player_id', $this->player->id);
        })->whereHas('result.fixture.season', function ($query) {
            $query->where('is_open', true);
        })->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $this->played = $this->frames->count();

        $this->frames->each(function ($frame) {
            if ($frame->home_player_id == $this->player->id) {
                if ($frame->home_score > $frame->away_score) {
                    $this->won++;
                }
            } else {
                if ($frame->away_score > $frame->home_score) {
                    $this->won++;
                }
            }
        });

        $this->lost = $this->played - $this->won;

        $this->role = $this->player->id == $this->player->team?->captain_id ? 'Captain' : 'Player';
        $this->role = $this->role != 'Captain' && $this->player->role == 2 ? 'Team Admin' : $this->role;

        $playerId = $this->player->id;
        
        $historyResult = DB::select("
                SELECT 
                    seasons.id AS season_id,
                    seasons.name AS season, 
                    s.name AS section, 
                    CASE 
                        WHEN fr.home_player_id = $playerId THEN r.home_team_name 
                        ELSE r.away_team_name 
                    END AS team, 
                    COUNT(*) AS played, 
                    COUNT(CASE WHEN (fr.home_player_id = $playerId AND fr.home_score > fr.away_score) OR (fr.away_player_id = $playerId AND fr.away_score > fr.home_score) THEN 1 END) AS won, 
                    COUNT(CASE WHEN (fr.home_player_id = $playerId AND fr.home_score < fr.away_score) OR (fr.away_player_id = $playerId AND fr.away_score < fr.home_score) THEN 1 END) AS lost 
                FROM 
                    sections AS s 
                JOIN 
                    seasons ON s.season_id = seasons.id 
                JOIN 
                    fixtures AS f ON s.id = f.section_id 
                JOIN 
                    results AS r ON f.id = r.fixture_id 
                JOIN 
                    frames AS fr ON fr.result_id = r.id 
                WHERE 
                    fr.home_player_id = $playerId OR fr.away_player_id = $playerId 
                GROUP BY 
                    s.name, seasons.name, CASE WHEN fr.home_player_id = $playerId THEN r.home_team_name ELSE r.away_team_name END 
        
                UNION ALL 
                
                SELECT 
                    '' AS season_id,
                    '' AS season, 
                    '' AS section, 
                    'Total' AS team, 
                    COUNT(*) AS played, 
                    COUNT(CASE WHEN (fr.home_player_id = $playerId AND fr.home_score > fr.away_score) OR (fr.away_player_id = $playerId AND fr.away_score > fr.home_score) THEN 1 END) AS won, 
                    COUNT(CASE WHEN (fr.home_player_id = $playerId AND fr.home_score < fr.away_score) OR (fr.away_player_id = $playerId AND fr.away_score < fr.home_score) THEN 1 END) AS lost 
                FROM 
                    sections AS s 
                JOIN 
                    seasons ON s.season_id = seasons.id 
                JOIN 
                    fixtures AS f ON s.id = f.section_id 
                JOIN 
                    results AS r ON f.id = r.fixture_id 
                JOIN 
                    frames AS fr ON fr.result_id = r.id 
                WHERE 
                    fr.home_player_id = $playerId OR fr.away_player_id = $playerId 
        
                ORDER BY 
                    CASE WHEN Team = 'Total' THEN 1 ELSE 0 END, season_id DESC"
        );
        
        // Convert the result to a collection
        $this->history = collect($historyResult);
        
    }

    public function render()
    {
        return view('livewire.player.show')
            ->layout('layouts.app', ['title' => $this->player->name]);
    }
}
