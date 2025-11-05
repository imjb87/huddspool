<?php

namespace Tests\Feature;

use App\Livewire\ResultForm;
use App\Models\Fixture;
use App\Models\FixtureResultLock;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ResultSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_admin_can_save_partial_frames(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $homePlayers = User::factory()->count(2)->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $awayPlayers = User::factory()->count(2)->create([
            'team_id' => $awayTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $this->actingAs($teamAdmin);

        $component = Livewire::test(ResultForm::class, ['fixture' => $fixture]);

        $component->set('frames.1.home_player_id', (string) $homePlayers[0]->id);
        $component->set('frames.1.away_player_id', (string) $awayPlayers[0]->id);
        $component->set('frames.1.home_score', 1);

        $result = Result::first();

        $this->assertNotNull($result);
        $this->assertFalse($result->is_confirmed);
        $this->assertSame(1, $result->home_score);
        $this->assertSame(0, $result->away_score);
        $this->assertSame($teamAdmin->id, $result->submitted_by);
        $this->assertCount(1, $result->frames);
        $this->assertEquals((int) $homePlayers[0]->id, $result->frames->first()->home_player_id);

        $component->assertSet('homeScore', 1);
        $component->assertSet('awayScore', 0);
    }

    public function test_locking_result_requires_all_frames_and_confirms_result(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $homePlayers = User::factory()->count(5)->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $awayPlayers = User::factory()->count(5)->create([
            'team_id' => $awayTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $this->actingAs($teamAdmin);

        $component = Livewire::test(ResultForm::class, ['fixture' => $fixture]);

        for ($i = 1; $i <= 10; $i++) {
            $homePlayer = $homePlayers[intdiv($i - 1, 2)];
            $awayPlayer = $awayPlayers[intdiv($i - 1, 2)];

            $component->set("frames.$i.home_player_id", (string) $homePlayer->id);
            $component->set("frames.$i.away_player_id", (string) $awayPlayer->id);

            if ($i % 2 === 1) {
                $component->set("frames.$i.home_score", 1);
                $component->set("frames.$i.away_score", 0);
            } else {
                $component->set("frames.$i.home_score", 0);
                $component->set("frames.$i.away_score", 1);
            }
        }

        $component->call('submit');

        $result = Result::first();

        $this->assertNotNull($result);
        $this->assertTrue($result->is_confirmed);
        $this->assertSame(5, $result->home_score);
        $this->assertSame(5, $result->away_score);
        $this->assertSame(10, $result->frames()->count());

        $component->assertRedirect(route('result.show', $result));
    }

    public function test_result_create_route_redirects_when_result_is_locked(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $lockedResult = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $teamAdmin->id,
            'is_confirmed' => true,
        ]);

        $this->actingAs($teamAdmin);

        $response = $this->get(route('result.create', $fixture));
        $response->assertRedirect(route('result.show', $lockedResult));

        $lockedResult->update(['is_confirmed' => false]);

        $response = $this->get(route('result.create', $fixture));
        $response->assertStatus(200);
    }

    public function test_team_admin_sees_continue_link_for_partial_result(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $homePlayer = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
        ]);

        $awayPlayer = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => 1,
        ]);

        $this->actingAs($teamAdmin);

        Livewire::test(ResultForm::class, ['fixture' => $fixture])
            ->set('frames.1.home_player_id', (string) $homePlayer->id)
            ->set('frames.1.away_player_id', (string) $awayPlayer->id)
            ->set('frames.1.home_score', 1);

        $result = Result::first();

        $response = $this->get(route('result.show', $result));
        $response->assertOk();
        $response->assertSeeText('Continue submitting result');

        // Non-admin team member should not see the link
        $nonAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $this->actingAs($nonAdmin);
        $response = $this->get(route('result.show', $result));
        $response->assertOk();
        $response->assertDontSeeText('Continue submitting result');
    }

    public function test_continue_link_hidden_for_confirmed_results(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'home_score' => 6,
            'away_score' => 4,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => true,
        ]);

        $this->actingAs($teamAdmin);

        $response = $this->get(route('result.show', $result));
        $response->assertOk();
        $response->assertDontSeeText('Continue submitting result');
    }

    public function test_only_one_team_admin_can_hold_the_edit_lock_at_a_time(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $primaryAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        $secondaryAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        Livewire::actingAs($primaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSet('canEdit', true)
            ->assertSet('lockedByAnother', false);

        $this->assertDatabaseHas('fixture_result_locks', [
            'fixture_id' => $fixture->id,
            'locked_by' => $primaryAdmin->id,
        ]);

        Livewire::actingAs($secondaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSet('canEdit', false)
            ->assertSet('lockedByAnother', true);

        FixtureResultLock::query()->update(['locked_until' => now()->subMinute()]);

        Livewire::actingAs($secondaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSet('canEdit', true)
            ->assertSet('lockedByAnother', false);
    }
}
