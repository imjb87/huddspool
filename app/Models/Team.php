<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'shortname',
        'venue_id',
        'folded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'folded_at' => 'datetime',
    ];

    /**
     * Get the venue associated with the team.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the players associated with the team.
     */
    public function players(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope teams that belong to the currently open season.
     */
    public function scopeInOpenSeason(Builder $query): Builder
    {
        return $query->whereHas('sections', function (Builder $sectionQuery) {
            $sectionQuery->whereHas('season', fn (Builder $seasonQuery) => $seasonQuery->where('is_open', true));
        });
    }

    /**
     * Get the sections the team belongs to.
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_team', 'team_id', 'section_id')
            ->withTimestamps();
    }

    /**
     * Get the sections the team belongs to in the currently open season.
     */
    public function openSections(): BelongsToMany
    {
        return $this->sections()->whereHas('season', function (Builder $query) {
            $query->where('is_open', true);
        });
    }

    /**
     * Get the first open-season section for the team.
     */
    public function openSection(): ?Section
    {
        if ($this->relationLoaded('openSections')) {
            return $this->getRelation('openSections')->first();
        }

        return $this->openSections()->first();
    }

    /**
     * Get the captain of the team from roles.
     */
    public function captain(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'captain_id');
    }

    /**
     * Get the home fixtures for the team.
     */
    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id')
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the away fixtures for the team.
     */
    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id')
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get all matches (home and away) for the team.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(Result::class, 'home_team_id')
            ->orWhere('results.away_team_id', $this->id);
    }

    /**
     * Get the results where the team played at home.
     */
    public function homeResults(): HasMany
    {
        return $this->hasMany(Result::class, 'home_team_id')
            ->whereHas('fixture.season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the results where the team played away.
     */
    public function awayResults(): HasMany
    {
        return $this->hasMany(Result::class, 'away_team_id')
            ->whereHas('fixture.season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the expulsions for the team.
     */
    public function expulsions(): MorphMany
    {
        return $this->morphMany(Expulsion::class, 'expellable');
    }

    public function sectionTeams(): HasMany
    {
        return $this->hasMany(SectionTeam::class);
    }
}
