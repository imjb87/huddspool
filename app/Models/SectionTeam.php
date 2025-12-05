<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Cache;
use App\Models\Section;

class SectionTeam extends Pivot
{
    protected $table = 'section_team';

    protected static function booted()
    {
        $flush = fn (SectionTeam $pivot) => $pivot->flushSectionCaches();

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'withdrawn_at' => 'datetime',
        'deducted' => 'integer',
        'sort' => 'integer',
    ];

    protected function flushSectionCaches(): void
    {
        if (! $this->section_id) {
            return;
        }

        Cache::forget(sprintf('section:%d:averages', $this->section_id));
        Cache::forget(sprintf('section:%d:standings', $this->section_id));
        Cache::forget('nav:past-seasons');
        Cache::forget('history:index');
        Cache::forget("team:season-history:{$this->team_id}");

        if ($section = Section::query()->select('season_id', 'ruleset_id')->find($this->section_id)) {
            if ($section->season_id) {
                Cache::forget(sprintf('history:season:%d', $section->season_id));
            }

            if ($section->season_id && $section->ruleset_id) {
                Cache::forget(sprintf('history:sections:%d:%d', $section->season_id, $section->ruleset_id));
            }
        }

    }

    public function results()
    {
        return $this->hasManyThrough(Result::class, Fixture::class, 'section_id', 'fixture_id', 'section_id', 'id')
            ->where(function ($query) {
                $query->where('fixtures.home_team_id', $this->team_id) // Reference the team_id directly
                      ->orWhere('fixtures.away_team_id', $this->team_id);
            });
    }

    // Computed Attributes
    public function getPlayedAttribute()
    {
        return $this->results->count(); // Dynamically filters results for this team
    }

    public function getWinsAttribute()
    {
        return $this->results->filter(function ($result) {
            return ($result->home_team_id == $this->team_id && $result->home_score > $result->away_score) ||
                   ($result->away_team_id == $this->team_id && $result->away_score > $result->home_score);
        })->count();
    }

    public function getDrawsAttribute()
    {
        return $this->results->filter(function ($result) {
            return $result->home_score == $result->away_score;
        })->count();
    }

    public function getLossesAttribute()
    {
        return $this->results->filter(function ($result) {
            return ($result->home_team_id == $this->team_id && $result->home_score < $result->away_score) ||
                   ($result->away_team_id == $this->team_id && $result->away_score < $result->home_score);
        })->count();
    }

    public function getPointsAttribute()
    {
        // Calculate the total points from the results
        $totalPoints = $this->results->sum(function ($result) {
            return $result->home_team_id == $this->team_id
                ? $result->home_score
                : $result->away_score;
        });
    
        // Subtract the deducted points (default to 0 if null)
        return max(0, $totalPoints - ($this->deducted ?? 0));
    }
}
