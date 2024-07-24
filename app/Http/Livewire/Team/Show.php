<?php

namespace App\Http\Livewire\Team;

use Livewire\Component;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class Show extends Component
{
    public Team $team;
    public $history = [];

    public function mount(Team $team)
    {
        if( $team->id == 1 ) {
            abort(404);
        }

        $this->team = $team;

        $teamId = $this->team->id;

        $historyResult = DB::select(
            DB::raw("
                WITH Standings AS (
                    SELECT 
                        season,
                        section_id,
                        section_name,
                        team_id,
                        team_name,
                        SUM(played) AS played,
                        SUM(won) AS won,
                        SUM(drawn) AS drawn,
                        SUM(lost) AS lost,
                        SUM(points) AS points,
                        RANK() OVER (PARTITION BY section_id ORDER BY points DESC, won DESC) AS rank
                    FROM (
                        SELECT 
                            s.name AS season,
                            f.section_id AS section_id,
                            se.name AS section_name,
                            r.home_team_id AS team_id,
                            r.home_team_name AS team_name,
                            COUNT(*) AS played,
                            SUM(CASE WHEN r.home_score > r.away_score THEN 1 ELSE 0 END) AS won,
                            SUM(CASE WHEN r.home_score = r.away_score THEN 1 ELSE 0 END) AS drawn,
                            SUM(CASE WHEN r.away_score > r.home_score THEN 1 ELSE 0 END) AS lost,
                            SUM(r.home_score) AS points
                        FROM results AS r 
                        JOIN fixtures AS f ON r.fixture_id = f.id
                        JOIN seasons AS s ON s.id = f.season_id
                        JOIN sections AS se ON se.id = f.section_id
                        WHERE f.section_id IN (
                            SELECT id 
                            FROM sections
                            WHERE id IN (
                                SELECT section_id 
                                FROM section_team
                                WHERE team_id = $teamId
                            )
                            AND deleted_at IS NULL
                        )
                        GROUP BY f.section_id, r.home_team_id
                        
                        UNION ALL
                        
                        SELECT 
                            s.name AS season,
                            f.section_id AS section_id,
                            se.name AS section_name,
                            r.away_team_id AS team_id,
                            r.away_team_name AS team_name,
                            COUNT(*) AS played,
                            SUM(CASE WHEN r.away_score > r.home_score THEN 1 ELSE 0 END) AS won,
                            SUM(CASE WHEN r.home_score = r.away_score THEN 1 ELSE 0 END) AS drawn,
                            SUM(CASE WHEN r.home_score > r.away_score THEN 1 ELSE 0 END) AS lost,
                            SUM(r.away_score) AS points
                        FROM results AS r 
                        JOIN fixtures AS f ON r.fixture_id = f.id
                        JOIN seasons AS s ON s.id = f.season_id
                        JOIN sections AS se ON se.id = f.section_id
                        WHERE f.section_id IN (
                            SELECT id 
                            FROM sections
                            WHERE id IN (
                                SELECT section_id 
                                FROM section_team
                                WHERE team_id = $teamId
                            )
                            AND deleted_at IS NULL
                        )
                        GROUP BY f.section_id, r.away_team_id
                    ) AS combined
                    GROUP BY section_id, team_id
                    ORDER BY section_id DESC, points DESC
                )
                SELECT * FROM Standings 
                WHERE team_id = $teamId;            
            ")
        );

        // Convert the result to a collection
        $this->history = collect($historyResult);
    }

    public function render()
    {
        return view('livewire.team.show')
            ->layout('layouts.app', ['title' => $this->team->name]);
    }
}
