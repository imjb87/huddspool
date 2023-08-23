<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'season_id',
        'ruleset_id',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('withdrawn_at')
            ->withTimestamps();
    }

    /**
     * Get the season that owns the section.
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the ruleset that owns the section.
     */
    public function ruleset()
    {
        return $this->belongsTo(Ruleset::class);
    }

    /**
     * Get the fixtures for the section.
     */
    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * Get the results for the section through fixtures.
     */
    public function results()
    {
        return $this->hasManyThrough(Result::class, Fixture::class);
    }

    public function standings()
    {
        $teams = $this->teams->map(function ($team) {
            $team->played = $this->results->filter(function ($result) use ($team) {
                return $result->home_team_id === $team->id || $result->away_team_id === $team->id;
            })->count();
            $team->wins = $this->results->filter(function ($result) use ($team) {
                return $result->home_team_id === $team->id && $result->home_score > $result->away_score
                    || $result->away_team_id === $team->id && $result->away_score > $result->home_score;
            })->count();
            $team->draws = $this->results->filter(function ($result) use ($team) {
                return $result->home_team_id === $team->id && $result->home_score === $result->away_score
                    || $result->away_team_id === $team->id && $result->away_score === $result->home_score;
            })->count();
            $team->losses = $this->results->filter(function ($result) use ($team) {
                return $result->home_team_id === $team->id && $result->home_score < $result->away_score
                    || $result->away_team_id === $team->id && $result->away_score < $result->home_score;
            })->count();
            $team->points = $this->results->where('home_team_id', $team->id)->sum('home_score')
                + $this->results->where('away_team_id', $team->id)->sum('away_score');

            return $team;
        })->sortByDesc(function ($team) {
            return $team->points * 10000 + $team->wins * 1000 + $team->draws * 100 + $team->played;
        });

        return $teams;
    }

}
