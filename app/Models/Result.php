<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Result extends Model
{
    use HasFactory, ClearsResponseCache, SoftDeletes;

    protected static function booted()
    {
        $flush = function (Result $result): void {
            foreach ([$result->home_team_id, $result->away_team_id] as $teamId) {
                if ($teamId) {
                    Cache::forget("team:season-history:{$teamId}");
                }
            }

            Cache::forget('stats:season-series');
            $result->loadMissing('section', 'fixture');

            $seasonId = $result->fixture?->season_id ?? $result->section?->season_id;
            $rulesetId = $result->fixture?->ruleset_id ?? $result->section?->ruleset_id;

            if ($seasonId) {
                Cache::forget(sprintf('history:season:%d', $seasonId));
            }

            if ($seasonId && $rulesetId) {
                Cache::forget(sprintf('history:sections:%d:%d', $seasonId, $rulesetId));
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fixture_id',
        'home_team_id',
        'home_team_name',
        'home_score',
        'away_team_id',
        'away_team_name',
        'away_score',
        'is_confirmed',
        'is_overridden',
        'submitted_by',
        'section_id',
        'ruleset_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_confirmed' => 'boolean',
        'is_overridden' => 'boolean',
    ];

    /**
     * Get the fixture that owns the result.
     */
    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * Get the result's frames.
     */
    public function frames()
    {
        return $this->hasMany(Frame::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function ruleset()
    {
        return $this->belongsTo(Ruleset::class);
    }

    /**
     * Scope results for the given team id(s).
     */
    public function scopeForTeam(Builder $query, Team|Collection|array|int $team): Builder
    {
        $teamIds = Fixture::normalizeTeamIds($team);

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
     * Scope results that belong to fixtures in an open season.
     */
    public function scopeInOpenSeason(Builder $query): Builder
    {
        return $query->whereHas('fixture.season', function (Builder $builder) {
            $builder->where('is_open', true);
        });
    }
    
}
