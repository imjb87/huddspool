<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Team;
use App\Models\Expulsion;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        $flush = function (Section $section): void {
            Cache::forget('history:index');
            Cache::forget('nav:past-seasons');
            Cache::forget(sprintf('section:%d:averages', $section->id));
            Cache::forget(sprintf('section:%d:standings', $section->id));

            if ($section->season_id && $section->ruleset_id) {
                Cache::forget(sprintf('history:sections:%d:%d', $section->season_id, $section->ruleset_id));
            }
        };

        static::saved($flush);
        static::deleted($flush);

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored($flush);
            static::forceDeleted($flush);
        }
    }

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
            ->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']);
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

    public function standings(): Collection
    {
        return Cache::remember($this->standingsCacheKey(), now()->addMinutes(2), function () {
            $results = $this->results()
                ->where('is_confirmed', true)
                ->get();

            $teamExpulsions = Expulsion::query()
                ->where('season_id', $this->season_id)
                ->where('expellable_type', Team::class)
                ->get()
                ->groupBy('expellable_id');

            $teams = $this->teams()
                ->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at'])
                ->withTrashed()
                ->get()
                ->map(function ($team) use ($results, $teamExpulsions) {
                    // Check if the team is expelled
                    $expelled = $teamExpulsions->has($team->id);

                    if ($expelled) {
                        $played = $wins = $draws = $losses = $points = 0;
                    } else {
                        $playedResults = $results->filter(function ($result) use ($team) {
                            return $result->home_team_id === $team->id || $result->away_team_id === $team->id;
                        });

                        $wins = $playedResults->filter(function ($result) use ($team) {
                            return ($result->home_team_id === $team->id && $result->home_score > $result->away_score)
                                || ($result->away_team_id === $team->id && $result->away_score > $result->home_score);
                        })->count();

                        $draws = $playedResults->filter(function ($result) use ($team) {
                            return ($result->home_team_id === $team->id && $result->home_score === $result->away_score)
                                || ($result->away_team_id === $team->id && $result->away_score === $result->home_score);
                        })->count();

                        $losses = $playedResults->filter(function ($result) use ($team) {
                            return ($result->home_team_id === $team->id && $result->home_score < $result->away_score)
                                || ($result->away_team_id === $team->id && $result->away_score < $result->home_score);
                        })->count();

                        $played = $playedResults->count();

                        $homePoints = $results->where('home_team_id', $team->id)->sum('home_score');
                        $awayPoints = $results->where('away_team_id', $team->id)->sum('away_score');

                        $points = $homePoints + $awayPoints - (int) ($team->pivot->deducted ?? 0);
                    }

                    return (object) [
                        'id' => $team->id,
                        'name' => $team->name,
                        'shortname' => $team->shortname,
                        'played' => $played,
                        'wins' => $wins,
                        'draws' => $draws,
                        'losses' => $losses,
                        'points' => $points,
                        'expelled' => $expelled,
                        'pivot' => (object) [
                            'withdrawn_at' => $team->pivot->withdrawn_at,
                            'deducted' => $team->pivot->deducted,
                        ],
                        'sort_score' => $expelled ? -1 : $points * 10000 + $wins * 1000 + $draws * 100 + $played,
                    ];
                })
                ->sortByDesc('sort_score')
                ->values()
                ->map(function ($team) {
                    unset($team->sort_score);
                    return $team;
                });

            return $teams;
        });
    }

    public function forgetStandingsCache(): void
    {
        Cache::forget($this->standingsCacheKey());
    }

    protected function standingsCacheKey(): string
    {
        return sprintf('section:%d:standings', $this->id);
    }
}
