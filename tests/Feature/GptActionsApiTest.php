<?php

namespace Tests\Feature;

use App\Models\Expulsion;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\GptActionAudit;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\NotificationSetting;
use App\Models\Page;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\SeasonEntry;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use daacreators\CreatorsTicketing\Models\Department;
use daacreators\CreatorsTicketing\Models\Ticket;
use daacreators\CreatorsTicketing\Models\TicketStatus;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GptActionsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_administrator_cannot_use_gpt_actions(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user, ['gpt:read']);

        $this->getJson(route('api.gpt.me'))
            ->assertForbidden()
            ->assertJsonPath('message', 'This Huddspool account is not authorised for GPT administration.');
    }

    public function test_administrator_can_search_players_without_exposing_contact_details(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        $player = User::factory()->create([
            'name' => 'Jamie Taylor',
            'email' => 'jamie@example.com',
            'telephone' => '07123456789',
            'team_id' => $team->id,
        ]);

        Passport::actingAs($admin, ['gpt:read']);

        $response = $this->getJson(route('api.gpt.players.index', ['query' => 'Jamie']));

        $response->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('players.0.id', $player->id)
            ->assertJsonPath('players.0.team.name', 'Black Horse Bandits')
            ->assertJsonMissing(['email' => 'jamie@example.com'])
            ->assertJsonMissing(['telephone' => '07123456789']);
    }

    public function test_administrator_can_move_player_and_change_captaincy_atomically(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $oldTeam = $this->createOpenSeasonTeam('Old Team');
        $newTeam = $this->createOpenSeasonTeam('New Team');
        $player = User::factory()->create([
            'name' => 'Jamie Taylor',
            'team_id' => $oldTeam->id,
        ]);
        $oldTeam->update(['captain_id' => $player->id]);

        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.players.team.update', $player), [
            'destination_team_id' => $newTeam->id,
            'expected_current_team_id' => $oldTeam->id,
            'make_destination_captain' => true,
        ])->assertOk()
            ->assertJsonPath('change.before.team_id', $oldTeam->id)
            ->assertJsonPath('change.before.was_captain', true)
            ->assertJsonPath('change.after.team_id', $newTeam->id)
            ->assertJsonPath('change.after.is_captain', true);

        $this->assertSame($newTeam->id, $player->refresh()->team_id);
        $this->assertNull($oldTeam->refresh()->captain_id);
        $this->assertSame($player->id, $newTeam->refresh()->captain_id);
        $this->assertDatabaseHas(GptActionAudit::class, [
            'administrator_id' => $admin->id,
            'action' => 'move_player',
            'subject_id' => $player->id,
        ]);
    }

    public function test_move_is_rejected_when_current_team_does_not_match_inspected_state(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $currentTeam = $this->createOpenSeasonTeam('Current Team');
        $staleTeam = $this->createOpenSeasonTeam('Stale Team');
        $newTeam = $this->createOpenSeasonTeam('New Team');
        $player = User::factory()->create(['team_id' => $currentTeam->id]);

        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.players.team.update', $player), [
            'destination_team_id' => $newTeam->id,
            'expected_current_team_id' => $staleTeam->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('expected_current_team_id');

        $this->assertSame($currentTeam->id, $player->refresh()->team_id);
        $this->assertDatabaseCount('gpt_action_audits', 0);
    }

    public function test_administrator_can_remove_player_from_team_and_leave_them_unassigned(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Old Team');
        $player = User::factory()->create([
            'name' => 'Jamie Taylor',
            'team_id' => $team->id,
        ]);
        $team->update(['captain_id' => $player->id]);

        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.command'), [
            'command' => 'move_player',
            'arguments' => [
                'player' => $player->id,
                'destination_team_id' => null,
                'expected_current_team_id' => $team->id,
            ],
        ])->assertOk()
            ->assertJsonPath('change.before.team_id', $team->id)
            ->assertJsonPath('change.before.was_captain', true)
            ->assertJsonPath('change.after.team_id', null)
            ->assertJsonPath('change.after.team_name', null)
            ->assertJsonPath('change.after.is_captain', false);

        $this->assertNull($player->refresh()->team_id);
        $this->assertNull($team->refresh()->captain_id);
        $this->assertDatabaseHas(GptActionAudit::class, [
            'administrator_id' => $admin->id,
            'action' => 'move_player',
            'subject_id' => $player->id,
        ]);
    }

    public function test_unassigned_player_cannot_be_made_captain(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Old Team');
        $player = User::factory()->create(['team_id' => $team->id]);

        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.players.team.update', $player), [
            'destination_team_id' => null,
            'expected_current_team_id' => $team->id,
            'make_destination_captain' => true,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('make_destination_captain');

        $this->assertSame($team->id, $player->refresh()->team_id);
        $this->assertDatabaseCount('gpt_action_audits', 0);
    }

    public function test_read_scope_cannot_perform_write_action(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $player = User::factory()->create();
        $team = $this->createOpenSeasonTeam('Destination');
        Passport::actingAs($admin, ['gpt:read']);

        $this->postJson(route('api.gpt.players.team.update', $player), [
            'destination_team_id' => $team->id,
            'expected_current_team_id' => null,
        ])->assertForbidden();
    }

    public function test_administrator_can_discover_only_explicitly_supported_resources(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Passport::actingAs($admin, ['gpt:read']);

        $this->getJson(route('api.gpt.capabilities'))
            ->assertOk()
            ->assertJsonFragment(['resource' => 'users'])
            ->assertJsonFragment(['resource' => 'knockout-matches'])
            ->assertJsonFragment(['resource' => 'support-tickets']);

        $this->getJson(route('api.gpt.resources.index', ['resource' => 'anything']))
            ->assertNotFound();
    }

    public function test_generic_resource_reads_use_field_and_relation_allowlists(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        $player = User::factory()->create([
            'name' => 'Jamie Taylor',
            'email' => 'jamie@example.com',
            'password' => 'secret-value',
            'remember_token' => 'private-token',
            'team_id' => $team->id,
        ]);
        Passport::actingAs($admin, ['gpt:read']);

        $this->getJson(route('api.gpt.resources.index', [
            'resource' => 'users',
            'search' => 'Jamie',
        ]))->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('records.0.id', $player->id)
            ->assertJsonPath('records.0.team.name', 'Black Horse Bandits')
            ->assertJsonMissing(['password' => 'secret-value'])
            ->assertJsonMissing(['remember_token' => 'private-token']);

        $this->getJson(route('api.gpt.resources.show', [
            'resource' => 'users',
            'record' => $player->id,
        ]))->assertOk()
            ->assertJsonPath('record.name', 'Jamie Taylor');
    }

    public function test_administrator_can_read_the_dashboard_summary(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Passport::actingAs($admin, ['gpt:read']);

        $this->getJson(route('api.gpt.dashboard'))
            ->assertOk()
            ->assertJsonStructure([
                'open_season' => ['total_frames', 'total_results', 'total_players'],
                'open_support_tickets',
                'outstanding_fixtures',
                'latest_results',
            ]);
    }

    public function test_administrator_can_read_an_ordered_team_roster(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        User::factory()->create(['name' => 'Jamie Taylor', 'team_id' => $team->id]);
        User::factory()->create(['name' => 'Ash Rees', 'team_id' => $team->id]);
        Passport::actingAs($admin, ['gpt:read']);

        $this->getJson(route('api.gpt.teams.roster', $team))
            ->assertOk()
            ->assertJsonPath('team.name', 'Black Horse Bandits')
            ->assertJsonPath('players.0.name', 'Ash Rees')
            ->assertJsonPath('players.1.name', 'Jamie Taylor');
    }

    public function test_administrator_can_read_player_history_and_browse_public_information(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create(['is_open' => false, 'name' => 'Archived Season']);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create(['season_id' => $season->id, 'ruleset_id' => $ruleset->id]);
        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();
        $player = User::factory()->create(['name' => 'History Player', 'team_id' => $team->id]);
        $opponent = User::factory()->create(['team_id' => $opponentTeam->id]);
        $fixture = Fixture::factory()->create(['season_id' => $season->id, 'section_id' => $section->id, 'ruleset_id' => $ruleset->id, 'home_team_id' => $team->id, 'away_team_id' => $opponentTeam->id]);
        $result = Result::factory()->create(['fixture_id' => $fixture->id, 'section_id' => $section->id, 'ruleset_id' => $ruleset->id, 'home_team_id' => $team->id, 'home_team_name' => $team->name, 'away_team_id' => $opponentTeam->id, 'away_team_name' => $opponentTeam->name]);
        Frame::query()->create(['result_id' => $result->id, 'home_player_id' => $player->id, 'home_score' => 1, 'away_player_id' => $opponent->id, 'away_score' => 0]);
        Passport::actingAs($admin, ['gpt:read']);

        $this->getJson(route('api.gpt.players.show', $player))
            ->assertOk()
            ->assertJsonPath('player.name', 'History Player')
            ->assertJsonPath('season_history.0.season_name', 'Archived Season')
            ->assertJsonPath('season_history.0.wins', 1);

        $this->getJson(route('api.gpt.browse', ['path' => "/players/{$player->id}"]))
            ->assertOk()
            ->assertJsonPath('path', "/players/{$player->id}")
            ->assertJsonPath('title', config('app.name'))
            ->assertJsonPath('content', fn (string $content): bool => str_contains($content, 'Archived Season')
                && ! str_contains($content, $admin->email)
                && ! str_contains($content, $player->email));

        $this->getJson(route('api.gpt.browse', ['path' => '/admin']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('path');
    }

    public function test_administrator_can_list_news_with_its_author(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Passport::actingAs($admin, ['gpt:read']);
        $news = News::query()->create([
            'title' => 'Season update',
            'content' => 'The season starts next week.',
            'published_at' => now(),
        ]);

        $this->getJson(route('api.gpt.resources.index', [
            'resource' => 'news',
            'limit' => 1,
        ]))->assertOk()
            ->assertJsonPath('records.0.id', $news->id)
            ->assertJsonPath('records.0.author.id', $admin->id);

        $this->getJson(route('api.gpt.resources.show', [
            'resource' => 'news',
            'record' => $news->id,
        ]))->assertOk()
            ->assertJsonPath('record.id', $news->id)
            ->assertJsonPath('record.author.id', $admin->id);
    }

    public function test_administrator_can_change_a_team_venue_with_an_audit_record(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create(['name' => 'Black Horse Bandits']);
        $opponent = Team::factory()->create();
        $otherTeam = Team::factory()->create();
        $oldVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $manualVenue = Venue::factory()->create();
        $team->update(['venue_id' => $oldVenue->id]);
        $section->teams()->attach($team, ['sort' => 1, 'deducted' => 0]);
        $section->teams()->attach($opponent, ['sort' => 2, 'deducted' => 0]);
        $section->teams()->attach($otherTeam, ['sort' => 3, 'deducted' => 0]);
        $qualifyingFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->addWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        $awayFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $opponent->id,
            'away_team_id' => $team->id,
            'fixture_date' => now()->addWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        $completedFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->addWeeks(2)->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        Result::factory()->create([
            'fixture_id' => $completedFixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'away_team_id' => $opponent->id,
            'away_team_name' => $opponent->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $historicalFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->subWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        $otherTeamFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $otherTeam->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->addWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        $manualOverrideFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $otherTeam->id,
            'fixture_date' => now()->addWeeks(3)->toDateString(),
            'venue_id' => $manualVenue->id,
        ]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.teams.venue.update', $team), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
            'update_future_home_fixtures' => true,
        ])->assertOk()
            ->assertJsonPath('change.before.venue_id', $oldVenue->id)
            ->assertJsonPath('change.after.venue_id', $newVenue->id)
            ->assertJsonPath('updated_fixture_count', 1);

        $this->assertSame($newVenue->id, $team->refresh()->venue_id);
        $this->assertSame($newVenue->id, $qualifyingFixture->refresh()->venue_id);
        $this->assertSame($oldVenue->id, $awayFixture->refresh()->venue_id);
        $this->assertSame($oldVenue->id, $completedFixture->refresh()->venue_id);
        $this->assertSame($oldVenue->id, $historicalFixture->refresh()->venue_id);
        $this->assertSame($oldVenue->id, $otherTeamFixture->refresh()->venue_id);
        $this->assertSame($manualVenue->id, $manualOverrideFixture->refresh()->venue_id);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_team_venue', 'subject_id' => $team->id]);
    }

    public function test_administrator_can_assign_only_a_current_team_player_as_captain(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        $captain = User::factory()->create(['team_id' => $team->id]);
        $outsider = User::factory()->create();
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.teams.captain.update', $team), [
            'captain_id' => $outsider->id,
            'expected_current_captain_id' => null,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('captain_id');

        $this->postJson(route('api.gpt.teams.captain.update', $team), [
            'captain_id' => $captain->id,
            'expected_current_captain_id' => null,
        ])->assertOk()
            ->assertJsonPath('change.after.captain_id', $captain->id);

        $this->assertSame($captain->id, $team->refresh()->captain_id);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_team_captain', 'subject_id' => $team->id]);
    }

    public function test_team_updates_are_rejected_when_the_inspected_state_is_stale(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        $currentVenue = Venue::factory()->create();
        $staleVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $team->update(['venue_id' => $currentVenue->id]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.teams.venue.update', $team), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $staleVenue->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('expected_current_venue_id');

        $this->assertSame($currentVenue->id, $team->refresh()->venue_id);
        $this->assertDatabaseMissing(GptActionAudit::class, ['action' => 'update_team_venue', 'subject_id' => $team->id]);
    }

    public function test_team_venue_changes_do_not_propagate_to_fixtures_without_the_explicit_opt_in_flag(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        $opponent = Team::factory()->create();
        $oldVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $team->update(['venue_id' => $oldVenue->id]);
        $fixture = Fixture::factory()->create([
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->addWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.teams.venue.update', $team), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
        ])->assertOk()->assertJsonPath('updated_fixture_count', 0);

        $this->assertSame($oldVenue->id, $fixture->refresh()->venue_id);
    }

    public function test_administrator_can_reschedule_a_fixture_with_a_stale_state_guard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $fixture = Fixture::factory()->create(['fixture_date' => '2026-08-04']);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.fixtures.date.update', $fixture), [
            'fixture_date' => '2026-08-11',
            'expected_current_fixture_date' => '2026-08-04',
        ])->assertOk()
            ->assertJsonPath('change.after.fixture_date', '2026-08-11');

        $this->assertSame('2026-08-11', $fixture->refresh()->fixture_date->toDateString());
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_fixture_date', 'subject_id' => $fixture->id]);
    }

    public function test_administrator_can_change_an_individual_fixture_venue_with_audit_and_stale_state_guards(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $oldVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $homeTeam = Team::factory()->create(['venue_id' => $oldVenue->id]);
        $awayTeam = Team::factory()->create();
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'venue_id' => $oldVenue->id,
            'fixture_date' => now()->addWeek()->toDateString(),
        ]);
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.command'), [
            'command' => 'set_fixture_venue',
            'arguments' => [
                'fixture' => $fixture->id,
                'venue_id' => $newVenue->id,
                'expected_current_venue_id' => $oldVenue->id,
                'expected_updated_at' => $fixture->updated_at->toJSON(),
                'reason' => 'Venue booking updated by the venue manager.',
            ],
        ])->assertOk()
            ->assertJsonPath('fixture.id', $fixture->id)
            ->assertJsonPath('fixture.venue.id', $newVenue->id)
            ->assertJsonPath('change.before.venue_id', $oldVenue->id)
            ->assertJsonPath('change.after.venue_id', $newVenue->id)
            ->assertJsonPath('change.after.reason', 'Venue booking updated by the venue manager.');

        $fixture->refresh();
        $this->assertSame($newVenue->id, $fixture->venue_id);
        $this->assertDatabaseHas(GptActionAudit::class, [
            'action' => 'update_fixture_venue',
            'subject_id' => $fixture->id,
        ]);
        $this->assertSame(
            GptActionAudit::query()->where('action', 'update_fixture_venue')->where('subject_id', $fixture->id)->firstOrFail()->id,
            $response->json('audit_id'),
        );
    }

    public function test_fixture_venue_command_rejects_invalid_identifiers_and_stale_fixture_state(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $oldVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $fixture = Fixture::factory()->create([
            'venue_id' => $oldVenue->id,
            'fixture_date' => now()->addWeek()->toDateString(),
        ]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.fixtures.venue.update', ['fixture' => 999999]), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
            'expected_updated_at' => $fixture->updated_at->toJSON(),
            'reason' => 'Venue booking updated by the venue manager.',
        ])->assertNotFound();

        $this->postJson(route('api.gpt.fixtures.venue.update', $fixture), [
            'venue_id' => 999999,
            'expected_current_venue_id' => $oldVenue->id,
            'expected_updated_at' => $fixture->updated_at->toJSON(),
            'reason' => 'Venue booking updated by the venue manager.',
        ])->assertUnprocessable()->assertJsonValidationErrors('venue_id');

        $this->postJson(route('api.gpt.fixtures.venue.update', $fixture), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => null,
            'expected_updated_at' => $fixture->updated_at->toJSON(),
            'reason' => 'Venue booking updated by the venue manager.',
        ])->assertUnprocessable()->assertJsonValidationErrors('expected_current_venue_id');

        $this->postJson(route('api.gpt.fixtures.venue.update', $fixture), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
            'expected_updated_at' => $fixture->updated_at->copy()->subMinute()->toJSON(),
            'reason' => 'Venue booking updated by the venue manager.',
        ])->assertUnprocessable()->assertJsonValidationErrors('expected_updated_at');

        $this->postJson(route('api.gpt.fixtures.venue.update', $fixture), [
            'venue_id' => $oldVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
            'expected_updated_at' => $fixture->updated_at->toJSON(),
            'reason' => 'Venue booking updated by the venue manager.',
        ])->assertUnprocessable()->assertJsonValidationErrors('venue_id');

        $this->assertDatabaseMissing(GptActionAudit::class, ['action' => 'update_fixture_venue', 'subject_id' => $fixture->id]);
    }

    public function test_non_administrator_cannot_change_a_fixture_venue(): void
    {
        $user = User::factory()->create();
        $oldVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $fixture = Fixture::factory()->create(['venue_id' => $oldVenue->id]);
        Passport::actingAs($user, ['gpt:write']);

        $this->postJson(route('api.gpt.fixtures.venue.update', $fixture), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
            'expected_updated_at' => $fixture->updated_at->toJSON(),
            'reason' => 'Venue booking updated by the venue manager.',
        ])->assertForbidden();

        $this->assertDatabaseMissing(GptActionAudit::class, ['action' => 'update_fixture_venue', 'subject_id' => $fixture->id]);
    }

    public function test_fixture_administration_record_includes_the_current_venue_result_state_and_updated_at(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $venue = Venue::factory()->create();
        $fixture = Fixture::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'venue_id' => $venue->id,
            'fixture_date' => now()->addWeek()->toDateString(),
        ]);
        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $fixture->section_id,
            'ruleset_id' => $fixture->ruleset_id,
        ]);
        Passport::actingAs($admin, ['gpt:read']);

        $this->getJson(route('api.gpt.resources.show', [
            'resource' => 'fixtures',
            'record' => $fixture->id,
        ]))->assertOk()
            ->assertJsonPath('record.id', $fixture->id)
            ->assertJsonPath('record.homeTeam.id', $homeTeam->id)
            ->assertJsonPath('record.awayTeam.id', $awayTeam->id)
            ->assertJsonPath('record.venue.id', $venue->id)
            ->assertJsonPath('record.result.id', $result->id)
            ->assertJsonStructure(['record' => ['updated_at']]);
    }

    public function test_migration_corrects_the_moldgreen_lib_bandits_fixture_regression_without_touching_exceptions(): void
    {
        $oldVenue = Venue::factory()->create([
            'id' => 44,
            'name' => 'The Black Horse',
        ]);
        $newVenue = Venue::factory()->create([
            'id' => 36,
            'name' => 'Moldgreen Liberal Club',
        ]);
        $team = Team::factory()->create([
            'id' => 73,
            'name' => 'Moldgreen Lib Bandits',
            'venue_id' => $newVenue->id,
        ]);
        $opponent = Team::factory()->create(['name' => "The Junction (K'Burton)"]);
        $futureFixture = Fixture::factory()->create([
            'id' => 16240,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->addWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        $historicalFixture = Fixture::factory()->create([
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->subWeek()->toDateString(),
            'venue_id' => $oldVenue->id,
        ]);
        $manualFixture = Fixture::factory()->create([
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now()->addWeeks(2)->toDateString(),
            'venue_id' => Venue::factory()->create()->id,
        ]);

        $migration = require base_path('database/migrations/2026_09_21_190000_propagate_moldgreen_lib_bandits_venue_change.php');
        $migration->up();
        $migration->up();

        $this->assertSame($newVenue->id, $futureFixture->refresh()->venue_id);
        $this->assertSame($oldVenue->id, $historicalFixture->refresh()->venue_id);
        $this->assertSame($manualFixture->venue_id, $manualFixture->fresh()->venue_id);
    }

    public function test_administrator_can_correct_a_complete_result_and_its_frames(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $fixture = Fixture::factory()->create(['home_team_id' => $homeTeam->id, 'away_team_id' => $awayTeam->id]);
        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'draft_version' => 3,
        ]);
        $homePlayers = User::factory()->count(5)->create(['team_id' => $homeTeam->id]);
        $awayPlayers = User::factory()->count(5)->create(['team_id' => $awayTeam->id]);
        $frames = collect(range(0, 9))->map(fn (int $index): array => [
            'home_player_id' => $homePlayers[$index % 5]->id,
            'away_player_id' => $awayPlayers[$index % 5]->id,
            'home_score' => $index < 6 ? 1 : 0,
            'away_score' => $index < 6 ? 0 : 1,
        ])->all();
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.results.correction', $result), [
            'expected_draft_version' => 3,
            'reason' => 'Correcting the signed scorecard.',
            'frames' => $frames,
        ])->assertOk()
            ->assertJsonPath('change.after.home_score', 6)
            ->assertJsonPath('change.after.away_score', 4)
            ->assertJsonPath('change.after.draft_version', 4);

        $result->refresh();
        $this->assertTrue($result->is_confirmed);
        $this->assertTrue($result->is_overridden);
        $this->assertCount(10, $result->frames);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'correct_result', 'subject_id' => $result->id]);
    }

    public function test_result_correction_rejects_wrong_team_players_and_stale_versions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();
        $fixture = Fixture::factory()->create(['home_team_id' => $homeTeam->id, 'away_team_id' => $awayTeam->id]);
        $result = Result::factory()->create(['fixture_id' => $fixture->id, 'draft_version' => 2]);
        $homePlayers = User::factory()->count(5)->create(['team_id' => $homeTeam->id]);
        $awayPlayers = User::factory()->count(5)->create(['team_id' => $awayTeam->id]);
        $frames = collect(range(0, 9))->map(fn (int $index): array => [
            'home_player_id' => $homePlayers[$index % 5]->id,
            'away_player_id' => $awayPlayers[$index % 5]->id,
            'home_score' => 1,
            'away_score' => 0,
        ])->all();
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.results.correction', $result), [
            'expected_draft_version' => 1,
            'reason' => 'Correcting the signed scorecard.',
            'frames' => $frames,
        ])->assertUnprocessable()->assertJsonValidationErrors('expected_draft_version');

        $frames[0]['home_player_id'] = $awayPlayers[0]->id;
        $this->postJson(route('api.gpt.results.correction', $result), [
            'expected_draft_version' => 2,
            'reason' => 'Correcting the signed scorecard.',
            'frames' => $frames,
        ])->assertUnprocessable()->assertJsonValidationErrors('frames.0.home_player_id');

        $this->assertSame(2, $result->refresh()->draft_version);
        $this->assertDatabaseMissing(GptActionAudit::class, ['action' => 'correct_result', 'subject_id' => $result->id]);
    }

    public function test_administrator_can_create_and_update_a_player_account(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = Team::factory()->create();
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.players.store'), [
            'name' => 'Jamie Taylor',
            'email' => 'jamie@example.com',
            'telephone' => null,
            'team_id' => $team->id,
            'site_role' => 'player',
        ])->assertCreated()
            ->assertJsonPath('player.name', 'Jamie Taylor')
            ->assertJsonPath('player.team_id', $team->id);

        $player = User::query()->findOrFail($response->json('player.id'));
        $this->patchJson(route('api.gpt.players.update', $player), [
            'expected_updated_at' => $player->updated_at->toAtomString(),
            'name' => 'James Taylor',
            'site_role' => 'team-admin',
        ])->assertOk()
            ->assertJsonPath('player.name', 'James Taylor')
            ->assertJsonPath('player.role', 'Team Admin');

        $this->assertTrue($player->refresh()->hasRole('team-admin'));
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'create_player', 'subject_id' => $player->id]);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_player', 'subject_id' => $player->id]);
    }

    public function test_player_account_update_rejects_stale_state(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $player = User::factory()->create(['name' => 'Jamie Taylor']);
        $staleUpdatedAt = $player->updated_at->copy()->subMinute();
        Passport::actingAs($admin, ['gpt:write']);

        $this->patchJson(route('api.gpt.players.update', $player), [
            'expected_updated_at' => $staleUpdatedAt->toAtomString(),
            'name' => 'James Taylor',
        ])->assertUnprocessable()->assertJsonValidationErrors('expected_updated_at');

        $this->assertSame('Jamie Taylor', $player->refresh()->name);
        $this->assertDatabaseMissing(GptActionAudit::class, ['action' => 'update_player', 'subject_id' => $player->id]);
    }

    public function test_player_creation_rejects_an_existing_name_case_insensitively(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        User::factory()->create(['name' => 'Jamie Taylor'])->delete();
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.players.store'), [
            'name' => 'jamie taylor',
            'site_role' => 'player',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('name')
            ->assertJsonPath('errors.name.0', 'A player with this name already exists. Find the existing account instead of creating a duplicate.');

        $this->assertSame(1, User::withTrashed()->whereRaw('LOWER(name) = ?', ['jamie taylor'])->count());
    }

    public function test_administrator_can_send_a_player_password_reset(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['is_admin' => true]);
        $player = User::factory()->create(['email' => 'jamie@example.com']);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.players.password-reset', $player))
            ->assertOk()
            ->assertJsonPath('player_id', $player->id);

        Notification::assertSentTo($player, ResetPassword::class);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'send_player_password_reset', 'subject_id' => $player->id]);
    }

    public function test_administrator_can_create_and_maintain_teams_and_venues(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Passport::actingAs($admin, ['gpt:write']);

        $venueResponse = $this->postJson(route('api.gpt.venues.store'), ['name' => 'The New Inn', 'address' => '1 High Street', 'telephone' => null])->assertCreated();
        $venue = Venue::query()->findOrFail($venueResponse->json('venue.id'));
        $teamResponse = $this->postJson(route('api.gpt.teams.store'), ['name' => 'New Inn A', 'shortname' => 'NIA', 'venue_id' => $venue->id])->assertCreated();
        $team = Team::query()->findOrFail($teamResponse->json('team.id'));

        $this->patchJson(route('api.gpt.teams.update', $team), ['expected_updated_at' => $team->updated_at->toAtomString(), 'shortname' => 'NEW'])->assertOk();
        $this->postJson(route('api.gpt.teams.fold', $team))->assertOk();

        $this->assertSame('NEW', $team->refresh()->shortname);
        $this->assertNotNull($team->folded_at);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'create_venue', 'subject_id' => $venue->id]);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'fold_team', 'subject_id' => $team->id]);
    }

    public function test_administrator_can_add_and_deduct_points_for_an_open_season_team(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create(['is_open' => true]);
        $section = Section::factory()->create(['season_id' => $season->id]);
        $team = Team::factory()->create();
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.sections.teams.store', $section->id), ['team_id' => $team->id])->assertCreated();
        $membership = SectionTeam::query()->findOrFail($response->json('section_team_id'));
        $this->patchJson(route('api.gpt.section-teams.deduction', $membership), ['deducted' => 2, 'expected_current_deduction' => 0])->assertOk();

        $this->assertSame(2, $membership->refresh()->deducted);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_points_deduction', 'subject_id' => $membership->id]);
    }

    public function test_deductions_and_withdrawals_are_rejected_for_closed_seasons(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create(['is_open' => false]);
        $section = Section::factory()->create(['season_id' => $season->id]);
        $membership = SectionTeam::query()->create(['section_id' => $section->id, 'team_id' => Team::factory()->create()->id, 'sort' => 1, 'deducted' => 0]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->patchJson(route('api.gpt.section-teams.deduction', $membership), ['deducted' => 2, 'expected_current_deduction' => 0])->assertUnprocessable()->assertJsonValidationErrors('section_team');
        $this->postJson(route('api.gpt.section-teams.withdraw', $membership))->assertUnprocessable()->assertJsonValidationErrors('section_team');

        $this->assertNull($membership->refresh()->withdrawn_at);
        $this->assertSame(0, $membership->deducted);
    }

    public function test_administrator_can_withdraw_a_team_from_the_open_season(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Team::factory()->create(['name' => Team::BYE_NAME]);
        $season = Season::factory()->create(['is_open' => true, 'dates' => [now()->subWeek()->toDateString(), now()->addWeek()->toDateString()]]);
        $section = Section::factory()->create(['season_id' => $season->id]);
        $membership = SectionTeam::query()->create(['section_id' => $section->id, 'team_id' => Team::factory()->create()->id, 'sort' => 1, 'deducted' => 0]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.section-teams.withdraw', $membership))->assertOk();

        $this->assertNotNull($membership->refresh()->withdrawn_at);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'withdraw_team_from_section', 'subject_id' => $membership->id]);
    }

    public function test_administrator_can_replace_a_section_team_without_recreating_competition_records(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create(['season_id' => $season->id, 'ruleset_id' => $ruleset->id]);
        $oldTeam = Team::factory()->create(['name' => "Marsh Lib 'A'"]);
        $replacementTeam = Team::factory()->create(['name' => 'Marsh Lib']);
        $opponent = Team::factory()->create();
        $membership = SectionTeam::query()->create([
            'section_id' => $section->id,
            'team_id' => $oldTeam->id,
            'sort' => 3,
            'deducted' => 2,
        ]);
        $fixture = Fixture::factory()->create([
            'section_id' => $section->id,
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $opponent->id,
            'away_team_id' => $oldTeam->id,
        ]);
        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $opponent->id,
            'home_team_name' => $opponent->name,
            'away_team_id' => $oldTeam->id,
            'away_team_name' => $oldTeam->name,
        ]);
        $frame = Frame::query()->create([
            'result_id' => $result->id,
            'home_player_id' => User::factory()->create(['team_id' => $opponent->id])->id,
            'away_player_id' => User::factory()->create(['team_id' => $oldTeam->id])->id,
            'home_score' => 1,
            'away_score' => 0,
        ]);
        $expectedUpdatedAt = $membership->updated_at->toAtomString();
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.command'), [
            'command' => 'replace_section_team',
            'arguments' => [
                'sectionTeam' => $membership->id,
                'replacement_team_id' => $replacementTeam->id,
                'expected_current_team_id' => $oldTeam->id,
                'expected_updated_at' => $expectedUpdatedAt,
                'reason' => 'Correcting the team identity entered for this section slot.',
            ],
        ])->assertOk();

        $this->assertSame($membership->id, $membership->refresh()->id);
        $this->assertSame($replacementTeam->id, $membership->team_id);
        $this->assertSame(3, $membership->sort);
        $this->assertSame(2, $membership->deducted);
        $this->assertSame($fixture->id, $fixture->refresh()->id);
        $this->assertSame($replacementTeam->id, $fixture->away_team_id);
        $this->assertSame($result->id, $result->refresh()->id);
        $this->assertSame($replacementTeam->id, $result->away_team_id);
        $this->assertSame('Marsh Lib', $result->away_team_name);
        $this->assertSame($frame->id, $frame->refresh()->id);
        $this->assertDatabaseHas(GptActionAudit::class, [
            'id' => $response->json('audit_id'),
            'administrator_id' => $admin->id,
            'action' => 'replace_section_team',
            'subject_id' => $membership->id,
        ]);
    }

    public function test_section_team_replacement_fails_for_stale_state_or_same_season_conflict(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create(['is_open' => true]);
        $section = Section::factory()->create(['season_id' => $season->id]);
        $otherSection = Section::factory()->create(['season_id' => $season->id]);
        $oldTeam = Team::factory()->create();
        $replacementTeam = Team::factory()->create();
        $membership = SectionTeam::query()->create(['section_id' => $section->id, 'team_id' => $oldTeam->id, 'sort' => 1]);
        SectionTeam::query()->create(['section_id' => $otherSection->id, 'team_id' => $replacementTeam->id, 'sort' => 1]);
        Passport::actingAs($admin, ['gpt:write']);
        $payload = [
            'replacement_team_id' => $replacementTeam->id,
            'expected_current_team_id' => $oldTeam->id,
            'expected_updated_at' => $membership->updated_at->toAtomString(),
            'reason' => 'Correcting an entry error.',
        ];

        $this->postJson(route('api.gpt.section-teams.replace-team', $membership), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('replacement_team_id');

        $replacementTeamTwo = Team::factory()->create();
        $this->postJson(route('api.gpt.section-teams.replace-team', $membership), array_merge($payload, [
            'replacement_team_id' => $replacementTeamTwo->id,
            'expected_current_team_id' => $replacementTeamTwo->id,
        ]))->assertUnprocessable()->assertJsonValidationErrors('expected_current_team_id');

        $this->postJson(route('api.gpt.section-teams.replace-team', $membership), array_merge($payload, [
            'replacement_team_id' => $replacementTeamTwo->id,
            'expected_updated_at' => $membership->updated_at->subMinute()->toAtomString(),
        ]))->assertUnprocessable()->assertJsonValidationErrors('expected_updated_at');

        $this->assertSame($oldTeam->id, $membership->refresh()->team_id);
        $this->assertDatabaseCount('gpt_action_audits', 0);
    }

    public function test_administrator_can_create_a_season_section_and_open_the_season(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $oldSeason = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        Passport::actingAs($admin, ['gpt:write']);
        $dates = collect(range(0, 17))->map(fn (int $week): string => now()->addWeeks($week)->toDateString())->all();

        $response = $this->postJson(route('api.gpt.seasons.store'), ['name' => 'Winter 2027', 'dates' => $dates, 'team_entry_fee' => 30, 'signup_opens_at' => null, 'signup_closes_at' => null])->assertCreated();
        $season = Season::query()->findOrFail($response->json('season_id'));
        $this->assertFalse($season->is_open);
        $this->postJson(route('api.gpt.sections.store'), ['name' => 'Section One', 'season_id' => $season->id, 'ruleset_id' => $ruleset->id])->assertCreated();
        $this->postJson(route('api.gpt.seasons.open', $season->id))->assertOk();

        $this->assertTrue($season->refresh()->is_open);
        $this->assertFalse($oldSeason->refresh()->is_open);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'open_season', 'subject_id' => $season->id]);
    }

    public function test_administrator_can_record_clear_and_forfeit_a_knockout_match(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $knockout = Knockout::factory()->create(['best_of' => 5]);
        $round = KnockoutRound::query()->create(['knockout_id' => $knockout->id, 'name' => 'Round 1', 'position' => 1, 'best_of' => 5, 'is_visible' => true]);
        $home = KnockoutParticipant::query()->create(['knockout_id' => $knockout->id, 'label' => 'Home']);
        $away = KnockoutParticipant::query()->create(['knockout_id' => $knockout->id, 'label' => 'Away']);
        $match = KnockoutMatch::query()->create(['knockout_id' => $knockout->id, 'knockout_round_id' => $round->id, 'position' => 1, 'home_participant_id' => $home->id, 'away_participant_id' => $away->id, 'best_of' => 5]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.knockout-matches.result', $match), ['home_score' => 3, 'away_score' => 1, 'reason' => 'Confirmed scorecard.', 'expected_completed_at' => null])->assertOk()->assertJsonPath('match.winner_participant_id', $home->id);
        $match->refresh();
        $this->postJson(route('api.gpt.knockout-matches.clear-result', $match), ['reason' => 'Result entered incorrectly.', 'expected_completed_at' => $match->completed_at->toAtomString()])->assertOk()->assertJsonPath('match.winner_participant_id', null);
        $this->postJson(route('api.gpt.knockout-matches.forfeit', $match), ['forfeit_participant_id' => $home->id, 'reason' => 'Home participant withdrew.', 'expected_completed_at' => null])->assertOk()->assertJsonPath('match.winner_participant_id', $away->id);

        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'record_knockout_result', 'subject_id' => $match->id]);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'clear_knockout_result', 'subject_id' => $match->id]);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'record_knockout_forfeit', 'subject_id' => $match->id]);
    }

    public function test_administrator_can_create_knockout_structure_without_duplicate_participants(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create();
        $player = User::factory()->create();
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.knockouts.store'), ['season_id' => $season->id, 'name' => 'Singles Cup', 'type' => 'singles', 'best_of' => 5, 'entry_fee' => 5])->assertCreated();
        $knockout = Knockout::query()->findOrFail($response->json('knockout_id'));
        $this->postJson(route('api.gpt.knockouts.participants.store', $knockout->id), ['player_one_id' => $player->id, 'seed' => 1])->assertCreated();
        $this->postJson(route('api.gpt.knockouts.participants.store', $knockout->id), ['player_one_id' => $player->id, 'seed' => 2])->assertUnprocessable()->assertJsonValidationErrors('participant');
        $this->postJson(route('api.gpt.knockouts.rounds.store', $knockout->id), ['name' => 'Round 1', 'position' => 1, 'scheduled_for' => now()->addWeek()->toAtomString(), 'best_of' => 5, 'is_visible' => true])->assertCreated();

        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'create_knockout', 'subject_id' => $knockout->id]);
        $this->assertDatabaseCount('knockout_participants', 1);
    }

    public function test_administrator_can_update_knockout_structure_with_stale_guards(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $knockout = Knockout::factory()->create(['type' => 'singles', 'best_of' => 5]);
        $player = User::factory()->create();
        $participant = KnockoutParticipant::query()->create(['knockout_id' => $knockout->id, 'player_one_id' => $player->id]);
        $round = KnockoutRound::query()->create(['knockout_id' => $knockout->id, 'name' => 'Round 1', 'position' => 1, 'is_visible' => true]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->patchJson(route('api.gpt.knockouts.update', $knockout->id), ['expected_updated_at' => $knockout->updated_at->toAtomString(), 'name' => 'Updated Singles', 'best_of' => 7])->assertOk();
        $this->patchJson(route('api.gpt.knockout-participants.update', $participant), ['expected_updated_at' => $participant->updated_at->toAtomString(), 'seed' => 2, 'label' => 'Seeded player'])->assertOk();
        $this->patchJson(route('api.gpt.knockout-rounds.update', $round), ['expected_updated_at' => $round->updated_at->toAtomString(), 'name' => 'Quarter-final', 'is_visible' => false])->assertOk();
        $this->patchJson(route('api.gpt.knockouts.update', $knockout->id), ['expected_updated_at' => now()->subMinute()->toAtomString(), 'name' => 'Stale'])->assertUnprocessable()->assertJsonValidationErrors('expected_updated_at');

        $this->assertSame('Updated Singles', $knockout->refresh()->name);
        $this->assertSame(2, $participant->refresh()->seed);
        $this->assertFalse($round->refresh()->is_visible);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_knockout_round', 'subject_id' => $round->id]);
    }

    public function test_administrator_can_update_support_ticket_workflow_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $assignee = User::factory()->create();
        $oldStatus = TicketStatus::query()->create(['name' => 'Open', 'slug' => 'open', 'is_default_for_new' => true]);
        $newStatus = TicketStatus::query()->create(['name' => 'Closed', 'slug' => 'closed', 'is_closing_status' => true]);
        $department = Department::query()->create(['name' => 'Support', 'slug' => 'support']);
        $ticket = Ticket::query()->create(['user_id' => $admin->id, 'department_id' => $department->id, 'ticket_status_id' => $oldStatus->id, 'priority' => 'low']);
        Passport::actingAs($admin, ['gpt:write']);

        $this->patchJson(route('api.gpt.support-tickets.update', $ticket), ['expected_updated_at' => $ticket->updated_at->toAtomString(), 'assignee_id' => $assignee->id, 'ticket_status_id' => $newStatus->id, 'priority' => 'high'])
            ->assertOk()
            ->assertJsonPath('ticket.assignee_id', $assignee->id)
            ->assertJsonPath('ticket.ticket_status_id', $newStatus->id)
            ->assertJsonPath('ticket.priority', 'high');

        $this->assertDatabaseHas($ticket->activities()->getModel()->getTable(), ['description' => 'Status was changed']);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'update_support_ticket', 'subject_id' => $ticket->id]);
    }

    public function test_administrator_can_delete_unreferenced_content_with_explicit_confirmation(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::query()->create(['title' => 'Temporary', 'slug' => 'temporary', 'content' => 'Temporary body']);
        Passport::actingAs($admin, ['gpt:write']);

        $this->deleteJson(route('api.gpt.resources.destroy', ['resource' => 'pages', 'record' => $page->id]), [
            'expected_updated_at' => $page->updated_at->toAtomString(),
            'reason' => 'Temporary page is no longer required.',
            'confirmation' => 'DELETE',
        ])->assertOk()->assertJsonPath('record_id', $page->id);

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $audit = GptActionAudit::query()->where('action', 'delete_pages')->firstOrFail();
        $this->assertArrayNotHasKey('content', $audit->before);
        $this->assertSame(strlen('Temporary body'), $audit->before['content_length']);
    }

    public function test_consolidated_command_gateway_preserves_existing_validation_and_auditing(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.command'), [
            'command' => 'create_content',
            'arguments' => ['resource' => 'pages', 'title' => 'Command page', 'slug' => 'command-page', 'content' => 'Created through the consolidated command.'],
        ])->assertCreated();

        $this->assertDatabaseHas('pages', ['id' => $response->json('record_id'), 'slug' => 'command-page']);
        $this->assertDatabaseHas(GptActionAudit::class, ['action' => 'create_pages', 'subject_id' => $response->json('record_id')]);
    }

    public function test_delete_action_protects_referenced_records_and_administrators(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $ruleset = Ruleset::factory()->create();
        $season = Season::factory()->create(['is_open' => false]);
        Section::factory()->create(['season_id' => $season->id, 'ruleset_id' => $ruleset->id]);
        Passport::actingAs($admin, ['gpt:write']);
        $payload = ['reason' => 'Confirmed administrative cleanup.', 'confirmation' => 'DELETE'];

        $this->deleteJson(route('api.gpt.resources.destroy', ['resource' => 'rulesets', 'record' => $ruleset->id]), $payload + ['expected_updated_at' => $ruleset->updated_at->toAtomString()])
            ->assertUnprocessable()->assertJsonValidationErrors('record');
        $this->deleteJson(route('api.gpt.resources.destroy', ['resource' => 'users', 'record' => $admin->id]), $payload + ['expected_updated_at' => $admin->updated_at->toAtomString()])
            ->assertUnprocessable()->assertJsonValidationErrors('record');
        $this->deleteJson(route('api.gpt.resources.destroy', ['resource' => 'teams', 'record' => 1]), $payload + ['expected_updated_at' => now()->toAtomString()])
            ->assertUnprocessable()->assertJsonValidationErrors('resource');
    }

    public function test_administrator_can_manage_expulsions_notifications_and_entry_payment(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $season = Season::factory()->create();
        $player = User::factory()->create();
        $setting = NotificationSetting::query()->create(['notification_type' => 'test_notice', 'name' => 'Test notice', 'description' => 'Test', 'enabled' => true]);
        $entry = SeasonEntry::factory()->create(['paid_at' => null]);
        Passport::actingAs($admin, ['gpt:write']);

        $response = $this->postJson(route('api.gpt.expulsions.store'), ['season_id' => $season->id, 'subject_type' => 'player', 'subject_id' => $player->id, 'reason' => 'Disciplinary decision.', 'date' => now()->toDateString()])->assertCreated();
        $this->assertNotNull(Expulsion::query()->find($response->json('expulsion_id')));
        $this->patchJson(route('api.gpt.notification-settings.update', $setting), ['enabled' => false, 'expected_enabled' => true])->assertOk()->assertJsonPath('enabled', false);
        $this->postJson(route('api.gpt.season-entries.mark-paid', $entry))->assertOk();

        $this->assertFalse($setting->refresh()->enabled);
        $this->assertTrue($entry->refresh()->isPaid());
    }

    public function test_administrator_can_create_and_update_managed_content_without_auditing_bodies(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Passport::actingAs($admin, ['gpt:write']);

        $pageResponse = $this->postJson(route('api.gpt.content.store', 'pages'), ['title' => 'About', 'slug' => 'about', 'content' => 'Original page body'])->assertCreated();
        $page = Page::query()->findOrFail($pageResponse->json('record_id'));
        $this->patchJson(route('api.gpt.content.update', ['resource' => 'pages', 'record' => $page->id]), ['expected_updated_at' => $page->updated_at->toAtomString(), 'content' => 'Updated page body'])->assertOk();
        $this->postJson(route('api.gpt.content.store', 'rulesets'), ['name' => 'International Rules', 'content' => 'Rules body'])->assertCreated();
        $this->postJson(route('api.gpt.content.store', 'news'), ['title' => 'Season update', 'content' => 'News body', 'published_at' => null])->assertCreated();

        $audit = GptActionAudit::query()->where('action', 'update_pages')->firstOrFail();
        $this->assertSame(strlen('Updated page body'), $audit->after['content_length']);
        $this->assertArrayNotHasKey('content', $audit->after);
        $this->assertSame('Updated page body', $page->refresh()->content);
    }

    public function test_administrator_can_view_the_oauth_authorization_prompt(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $client = app(ClientRepository::class)->createAuthorizationCodeGrantClient(
            name: 'Huddspool administrator GPT',
            redirectUris: ['https://chatgpt.com/aip/example/oauth/callback'],
        );

        $this->actingAs($admin)
            ->get(route('passport.authorizations.authorize', [
                'client_id' => $client->getKey(),
                'redirect_uri' => 'https://chatgpt.com/aip/example/oauth/callback',
                'response_type' => 'code',
                'scope' => 'gpt:read gpt:write',
                'state' => 'test-state',
            ]))
            ->assertOk()
            ->assertSee('Connect Huddspool')
            ->assertSee('Allow access');
    }

    private function createOpenSeasonTeam(string $name): Team
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create(['name' => $name]);
        $section->teams()->attach($team, ['sort' => 1, 'deducted' => 0]);

        return $team;
    }
}
