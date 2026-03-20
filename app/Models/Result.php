<?php

namespace App\Models;

use App\Support\CompetitionCacheInvalidator;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Result extends Model
{
    use ClearsResponseCache, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        $cacheInvalidator = app(CompetitionCacheInvalidator::class);
        $flush = function (Result $result) use ($cacheInvalidator): void {
            $cacheInvalidator->forgetForResult($result);
        };

        static::saved($flush);
        static::deleted($flush);
        static::restored($flush);
        static::forceDeleted($flush);
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
        'submitted_at',
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
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the fixture that owns the result.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * Get the result's frames.
     */
    public function frames(): HasMany
    {
        return $this->hasMany(Frame::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function ruleset(): BelongsTo
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
