<?php

// app/Queries/GetSectionAverages.php

namespace App\Queries;

use Illuminate\Support\Facades\DB;
use App\Models\Section;

class GetSectionAverages
{
    public function __construct(
        protected Section $section,
        protected int $page = 1,
        protected int $perPage = 10
    ) {}

    public function __invoke()
    {
        $sql = <<<SQL
SELECT 
    u.id,
    u.name,
    COUNT(f.id) AS frames_played,
    SUM(
        CASE 
            WHEN (u.id = f.home_player_id AND f.home_score > f.away_score)
              OR (u.id = f.away_player_id AND f.away_score > f.home_score)
            THEN 1 ELSE 0 
        END
    ) AS frames_won,
    SUM(
        CASE 
            WHEN (u.id = f.home_player_id AND f.home_score < f.away_score)
              OR (u.id = f.away_player_id AND f.away_score < f.home_score)
            THEN 1 ELSE 0 
        END
    ) AS frames_lost,
    ROUND(
        100.0 * SUM(
            CASE 
                WHEN (u.id = f.home_player_id AND f.home_score > f.away_score)
                  OR (u.id = f.away_player_id AND f.away_score > f.home_score)
                THEN 1 ELSE 0 
            END
        ) / NULLIF(COUNT(f.id), 0), 1
    ) AS frames_won_pct,
    ROUND(
        100.0 * SUM(
            CASE 
                WHEN (u.id = f.home_player_id AND f.home_score < f.away_score)
                  OR (u.id = f.away_player_id AND f.away_score < f.home_score)
                THEN 1 ELSE 0 
            END
        ) / NULLIF(COUNT(f.id), 0), 1
    ) AS frames_lost_pct
FROM frames f
JOIN users u 
  ON u.id = f.home_player_id OR u.id = f.away_player_id
JOIN results r 
  ON r.id = f.result_id
WHERE 
  r.section_id = ?
  AND u.id NOT IN (
      SELECT e.expellable_id
      FROM expulsions e
      WHERE e.expellable_type = 'App\\Models\\User'
        AND e.season_id = ?
  )
GROUP BY u.id, u.name
ORDER BY 
  frames_won_pct DESC,
  frames_won DESC,
  frames_lost ASC,
  u.name ASC
LIMIT ? OFFSET ?
SQL;

        return DB::select($sql, [
            $this->section->id,
            $this->section->season_id,           // <- use the season id here
            $this->perPage,
            ($this->page - 1) * $this->perPage,
        ]);
    }
}
