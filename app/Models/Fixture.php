<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Traits\ClearsResponseCache;

class Fixture extends Model
{
    use HasFactory, ClearsResponseCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'week',
        'fixture_date',
        'home_team_id',
        'away_team_id',
        'season_id',
        'section_id',
        'venue_id',
        'ruleset_id'
    ];

    protected $casts = [
        'fixture_date' => 'datetime',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id')->withTrashed();
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id')->withTrashed();
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function ruleset()
    {
        return $this->belongsTo(Ruleset::class);
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }

    /**
     * Scope fixtures for the given team id(s).
     */
    public function scopeForTeam(Builder $query, Team|Collection|array|int $team): Builder
    {
        $teamIds = static::normalizeTeamIds($team);

        if (empty($teamIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $builder) use ($teamIds) {
            $builder
                ->whereIn('home_team_id', $teamIds)
                ->orWhereIn('away_team_id', $teamIds);
        });
    }

    /**
     * Scope fixtures that belong to an open season.
     */
    public function scopeInOpenSeason(Builder $query): Builder
    {
        return $query->whereHas('season', function (Builder $builder) {
            $builder->where('is_open', true);
        });
    }

    /**
     * Normalise various team inputs into a list of ids.
     *
     * @param  Team|Collection<int, Team>|array<int, int|Team>|int  $team
     * @return array<int, int>
     */
    public static function normalizeTeamIds(Team|Collection|array|int $team): array
    {
        return collect(Arr::wrap($team))
            ->map(function ($value) {
                if ($value instanceof Team) {
                    return $value->getKey();
                }

                if ($value instanceof Collection) {
                    return $value->pluck('id');
                }

                return $value;
            })
            ->flatten()
            ->filter(fn ($value) => ! is_null($value))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();
    }
}
