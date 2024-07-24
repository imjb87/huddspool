<?php

namespace App\Http\Livewire\Player;

use Livewire\Component;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SectionShow extends Component
{
    public Section $section;
    public $players;
    public $page = 1;
    public $totalPages;
    public $history;

    public function mount(Section $section, $history)
    {
        $this->section = $section;
        $this->history = $history;

        $sectionId = $this->section->id;

        $perPage = 10;
        
        $totalPlayers = DB::selectOne(
            'SELECT COUNT(DISTINCT users.id) as total_players
            FROM users
            JOIN frames ON users.id = frames.home_player_id OR users.id = frames.away_player_id
            JOIN results ON frames.result_id = results.id
            JOIN fixtures ON results.fixture_id = fixtures.id
            JOIN sections ON fixtures.section_id = sections.id
            WHERE sections.id = ?',
            [$sectionId]
        );
        
        $this->totalPages = ceil($totalPlayers->total_players / $perPage);

        $this->players = DB::select(
            'SELECT users.id, users.name
            , CASE WHEN frames.home_player_id = users.id THEN results.home_team_name ELSE results.away_team_name END AS team_name
            , SUM(CASE WHEN frames.home_player_id = users.id THEN frames.home_score ELSE frames.away_score END) AS total_score
            , SUM(CASE WHEN frames.home_player_id = users.id THEN frames.away_score ELSE frames.home_score END) AS total_against
            , COUNT(frames.id) AS total_frames
            FROM users
            JOIN frames ON users.id = frames.home_player_id OR users.id = frames.away_player_id
            JOIN results ON frames.result_id = results.id
            JOIN fixtures ON results.fixture_id = fixtures.id
            JOIN sections ON fixtures.section_id = sections.id
            WHERE sections.id = ?
            GROUP BY users.id, users.name
            ORDER BY total_score DESC, total_against ASC, total_frames DESC, users.name ASC
            LIMIT ?
            OFFSET ?',
            [$sectionId, $perPage, ($this->page - 1) * $perPage]
        );

    }

    public function nextPage()
    {
        $this->page++;
        $this->mount($this->section, $this->history);
    }

    public function previousPage()
    {
        $this->page--;
        $this->mount($this->section, $this->history);
    }

    public function render()
    {
        return view('livewire.player.section-show');
    }
}
