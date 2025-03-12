<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

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
        return $this->belongsToMany(Team::class, 'section_team', 'section_id', 'team_id')
            ->using(SectionTeam::class) // Reference the custom pivot model
            ->withTimestamps()
            ->withPivot(['sort', 'section_id', 'team_id', 'deducted']);
    }
    
    public function players()
    {
        return User::whereIn('team_id', $this->teams()->pluck('teams.id'))->get();
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

    public function generateFixtures()
    {
        $fixture_service = new \App\Services\FixtureService($this);
        $schedule = $fixture_service->generate($this);

        foreach ($schedule as $fixtures) {
            foreach ($fixtures as $fixture) {
                $this->fixtures()->create($fixture);
            }
        }
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
            // Check if the team is expelled
            $expulsion = $team->expulsions()
                ->where('season_id', $this->season_id)
                ->first();
    
            if ($expulsion) {
                // If expelled, set all figures to 0
                $team->played = 0;
                $team->wins = 0;
                $team->draws = 0;
                $team->losses = 0;
                $team->points = 0;
    
                // Add a flag to mark the team as expelled
                $team->expelled = true;
            } else {
                // Calculate standings as usual
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
                $team->points = ($this->results->where('home_team_id', $team->id)->sum('home_score')
                    + $this->results->where('away_team_id', $team->id)->sum('away_score'))
                    - $team->pivot->deducted;
    
                $team->expelled = false;
            }
    
            return $team;
        });
    
        // Sort teams: expelled teams go last, and others are sorted by points, wins, draws, and played
        $sortedTeams = $teams->sortByDesc(function ($team) {
            return $team->expelled
                ? -1 // Expelled teams are sorted to the bottom
                : $team->points * 10000 + $team->wins * 1000 + $team->draws * 100 + $team->played;
        });
    
        return $sortedTeams;
    }    
}
