<?php

namespace App\Models;

use App\Support\CompetitionCacheInvalidator;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SectionTeam extends Pivot
{
    public const int WRAP_SORT_POSITION = 10;

    protected $table = 'section_team';

    public $incrementing = true;

    protected static function booted(): void
    {
        $cacheInvalidator = app(CompetitionCacheInvalidator::class);
        $flush = function (SectionTeam $pivot) use ($cacheInvalidator): void {
            $cacheInvalidator->forgetForSectionTeam($pivot);
        };

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

    public static function displaySortValue(int $sort): int
    {
        return $sort === self::WRAP_SORT_POSITION ? 0 : $sort;
    }

    public function getDisplaySortAttribute(): int
    {
        return self::displaySortValue((int) $this->sort);
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
