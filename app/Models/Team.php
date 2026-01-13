<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the players associated with the team.
     */
    public function players()
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
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_team', 'team_id', 'section_id')
            ->withTimestamps();
    }

    /**
     * Get the active section for the team.
     */
    public function section()
    {
        return $this->sections()->whereHas('season', function ($query) {
            $query->where('is_open', true);
        })->first();
    }

    /**
     * Get the captain of the team from roles.
     */
    public function captain()
    {
        return $this->hasOne(User::class, 'id', 'captain_id');
    }

    /**
     * Get all fixtures (home and away) for the team.
     */
    public function fixtures(): Builder
    {
        return Fixture::query()
            ->forTeam($this)
            ->inOpenSeason();
    }

    /**
     * Get the home fixtures for the team.
     */
    public function homeFixtures()
    {
        return $this->hasMany(Fixture::class, 'home_team_id')
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the away fixtures for the team.
     */
    public function awayFixtures()
    {
        return $this->hasMany(Fixture::class, 'away_team_id')
            ->whereHas('season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the results for the team.
     */
    public function results(): Builder
    {
        return Result::query()
            ->forTeam($this)
            ->inOpenSeason();
    }

    /**
     * Get all matches (home and away) for the team.
     */
    public function matches()
    {
        return $this->hasMany(Result::class, 'home_team_id')
            ->orWhere('results.away_team_id', $this->id);
    }

    /**
     * Get the results where the team played at home.
     */
    public function homeResults()
    {
        return $this->hasMany(Result::class, 'home_team_id')
            ->whereHas('fixture.season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the results where the team played away.
     */
    public function awayResults()
    {
        return $this->hasMany(Result::class, 'away_team_id')
            ->whereHas('fixture.season', function ($query) {
                $query->where('is_open', true);
            });
    }

    /**
     * Get the expulsions for the team.
     */
    public function expulsions()
    {
        return $this->morphMany(Expulsion::class, 'expellable');
    }

    public function sectionTeams()
    {
        return $this->hasMany(SectionTeam::class);
    }
}
