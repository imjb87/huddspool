<?php

namespace App\Models;

use App\Services\FixtureService;
use App\Support\CompetitionCacheInvalidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        $cacheInvalidator = new CompetitionCacheInvalidator;

        static::deleting(function (Section $section) {
            if ($section->hasRecordedResults()) {
                return false;
            }
        });

        static::saving(function (Section $section): void {
            if ($section->isDirty('name') || blank($section->slug)) {
                $section->slug = $section->generateSlug($section->name, $section->id);
            }
        });

        $flush = function (Section $section) use ($cacheInvalidator): void {
            $cacheInvalidator->forgetForSection($section);
        };

        static::saved($flush);
        static::deleted(function (Section $section) use ($flush): void {
            if ($section->trashed()) {
                $section->releaseArchivedSlug();
            }

            $flush($section);
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            static::restored(function (Section $section) use ($flush): void {
                $section->refreshSlug();
                $flush($section);
            });
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
        'slug',
        'season_id',
        'ruleset_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'section_team', 'section_id', 'team_id')
            ->using(SectionTeam::class) // Reference the custom pivot model
            ->withTimestamps()
            ->withPivot(['id', 'sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']);
    }

    public function sectionTeams(): HasMany
    {
        return $this->hasMany(SectionTeam::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'section_team', 'section_id', 'team_id', 'id', 'team_id')
            ->whereHas('team');
    }

    /**
     * Get the season that owns the section.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the ruleset that owns the section.
     */
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    /**
     * Get the fixtures for the section.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function generateFixtures(): void
    {
        $fixtureService = new FixtureService($this);
        $schedule = $fixtureService->generate();

        foreach ($schedule as $fixtures) {
            foreach ($fixtures as $fixture) {
                $this->fixtures()->create($fixture);
            }
        }
    }

    /**
     * Get the results for the section through fixtures.
     */
    public function results(): HasManyThrough
    {
        return $this->hasManyThrough(Result::class, Fixture::class);
    }

    public function scopeWithStandingsRelations(Builder $query): Builder
    {
        return $query->with([
            'results' => fn (HasManyThrough $query) => $query->where('is_confirmed', true),
            'season' => fn (BelongsTo $query) => $query->with('expulsions'),
            'teams' => fn (BelongsToMany $query) => $query->withTrashed()->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']),
        ]);
    }

    public function hasRecordedResults(): bool
    {
        return $this->results()->exists();
    }

    public function standings(): Collection
    {
        return Cache::remember($this->standingsCacheKey(), now()->addMinutes(2), function () {
            $results = $this->relationLoaded('results')
                ? $this->results->where('is_confirmed', true)->values()
                : $this->results()
                    ->where('is_confirmed', true)
                    ->get();

            $teamExpulsions = $this->relationLoaded('season') && $this->season?->relationLoaded('expulsions')
                ? $this->season->expulsions
                    ->where('expellable_type', Team::class)
                    ->groupBy('expellable_id')
                : Expulsion::query()
                    ->where('season_id', $this->season_id)
                    ->where('expellable_type', Team::class)
                    ->get()
                    ->groupBy('expellable_id');

            $teamStats = [];

            foreach ($results as $result) {
                $homeTeamId = $result->home_team_id;
                $awayTeamId = $result->away_team_id;

                $teamStats[$homeTeamId] ??= [
                    'played' => 0,
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'points' => 0,
                    'latest_result_id' => null,
                    'archived_name' => null,
                ];

                $teamStats[$awayTeamId] ??= [
                    'played' => 0,
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'points' => 0,
                    'latest_result_id' => null,
                    'archived_name' => null,
                ];

                $teamStats[$homeTeamId]['played']++;
                $teamStats[$awayTeamId]['played']++;

                $teamStats[$homeTeamId]['points'] += $result->home_score;
                $teamStats[$awayTeamId]['points'] += $result->away_score;

                if ($result->home_score > $result->away_score) {
                    $teamStats[$homeTeamId]['wins']++;
                    $teamStats[$awayTeamId]['losses']++;
                } elseif ($result->home_score < $result->away_score) {
                    $teamStats[$homeTeamId]['losses']++;
                    $teamStats[$awayTeamId]['wins']++;
                } else {
                    $teamStats[$homeTeamId]['draws']++;
                    $teamStats[$awayTeamId]['draws']++;
                }

                if ($teamStats[$homeTeamId]['latest_result_id'] === null || $result->id > $teamStats[$homeTeamId]['latest_result_id']) {
                    $teamStats[$homeTeamId]['latest_result_id'] = $result->id;
                    $teamStats[$homeTeamId]['archived_name'] = $result->home_team_name;
                }

                if ($teamStats[$awayTeamId]['latest_result_id'] === null || $result->id > $teamStats[$awayTeamId]['latest_result_id']) {
                    $teamStats[$awayTeamId]['latest_result_id'] = $result->id;
                    $teamStats[$awayTeamId]['archived_name'] = $result->away_team_name;
                }
            }

            $teams = ($this->relationLoaded('teams')
                ? $this->teams
                : $this->teams()
                    ->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at'])
                    ->withTrashed()
                    ->get())
                ->map(function ($team) use ($teamExpulsions, $teamStats) {
                    $expelled = $teamExpulsions->has($team->id);
                    $stats = $teamStats[$team->id] ?? [
                        'played' => 0,
                        'wins' => 0,
                        'draws' => 0,
                        'losses' => 0,
                        'points' => 0,
                        'archived_name' => null,
                    ];

                    $archivedName = $stats['archived_name'];

                    if ($expelled) {
                        $played = $wins = $draws = $losses = $points = 0;
                    } else {
                        $played = $stats['played'];
                        $wins = $stats['wins'];
                        $draws = $stats['draws'];
                        $losses = $stats['losses'];
                        $points = $stats['points'] - (int) ($team->pivot->deducted ?? 0);
                    }

                    return (object) [
                        'id' => $team->id,
                        'name' => $team->name,
                        'archived_name' => $archivedName,
                        'shortname' => $team->shortname,
                        'trashed' => method_exists($team, 'trashed') ? $team->trashed() : false,
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

    private function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'section';
        $original = $slug;
        $suffix = 1;

        while (self::query()
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->where('season_id', $this->season_id)
            ->where('ruleset_id', $this->ruleset_id)
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$original}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function standingsCacheKey(): string
    {
        return sprintf('section:%d:standings', $this->id);
    }

    private function releaseArchivedSlug(): void
    {
        $archivedSlug = sprintf('%s-archived-%d', Str::slug($this->name) ?: 'section', $this->id);

        if ($this->slug === $archivedSlug) {
            return;
        }

        $this->forceFill(['slug' => $archivedSlug])->saveQuietly();
    }

    private function refreshSlug(): void
    {
        $slug = $this->generateSlug($this->name, $this->id);

        if ($this->slug === $slug) {
            return;
        }

        $this->forceFill(['slug' => $slug])->saveQuietly();
    }
}
