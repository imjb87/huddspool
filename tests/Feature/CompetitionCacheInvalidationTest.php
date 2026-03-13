<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CompetitionCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_restoring_a_result_clears_result_related_caches(): void
    {
        Cache::flush();

        [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'fixture' => $fixture,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
        ] = $this->createFixtureContext();

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $result->delete();

        $keys = [
            "team:season-history:{$homeTeam->id}",
            "team:season-history:{$awayTeam->id}",
            'stats:season-series',
            'stats:season-series-chart',
            sprintf('history:season:%d', $season->id),
            sprintf('history:sections:%d:%d', $season->id, $ruleset->id),
        ];

        $this->cacheKeys($keys);

        $result->restore();

        $this->assertCacheKeysCleared($keys);
    }

    public function test_restoring_a_frame_clears_frame_related_caches(): void
    {
        Cache::flush();

        [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'fixture' => $fixture,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
            'homePlayer' => $homePlayer,
            'awayPlayer' => $awayPlayer,
        ] = $this->createFixtureContext();

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $frame = Frame::create([
            'result_id' => $result->id,
            'home_player_id' => $homePlayer->id,
            'home_score' => 1,
            'away_player_id' => $awayPlayer->id,
            'away_score' => 0,
        ]);

        $frame->delete();

        $keys = [
            'stats:open-season',
            'stats:season-series',
            'stats:season-series-chart',
            'history:index',
            'nav:past-seasons',
            sprintf('section:%d:averages', $section->id),
            sprintf('section:%d:standings', $section->id),
            sprintf('history:season:%d', $season->id),
            sprintf('history:sections:%d:%d', $season->id, $ruleset->id),
            "player:season-history:{$homePlayer->id}",
            "player:season-history:{$awayPlayer->id}",
            "team:season-history:{$homeTeam->id}",
            "team:season-history:{$awayTeam->id}",
        ];

        $this->cacheKeys($keys);

        $frame->restore();

        $this->assertCacheKeysCleared($keys);
    }

    /**
     * @return array{
     *     season: Season,
     *     ruleset: Ruleset,
     *     section: Section,
     *     fixture: Fixture,
     *     homeTeam: Team,
     *     awayTeam: Team,
     *     homePlayer: User,
     *     awayPlayer: User
     * }
     */
    private function createFixtureContext(): array
    {
        $season = Season::factory()->create();
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $homePlayer = User::factory()->create(['team_id' => $homeTeam->id]);
        $awayPlayer = User::factory()->create(['team_id' => $awayTeam->id]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
        ]);

        return [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
            'fixture' => $fixture,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
            'homePlayer' => $homePlayer,
            'awayPlayer' => $awayPlayer,
        ];
    }

    /**
     * @param  array<int, string>  $keys
     */
    private function cacheKeys(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::put($key, true, now()->addMinutes(10));
            $this->assertTrue(Cache::has($key));
        }
    }

    /**
     * @param  array<int, string>  $keys
     */
    private function assertCacheKeysCleared(array $keys): void
    {
        foreach ($keys as $key) {
            $this->assertFalse(Cache::has($key), "Expected cache key [{$key}] to be cleared.");
        }
    }
}
