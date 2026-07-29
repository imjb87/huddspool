<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\GptActionAudit;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
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
        $team = $this->createOpenSeasonTeam('Black Horse Bandits');
        $oldVenue = Venue::factory()->create();
        $newVenue = Venue::factory()->create();
        $team->update(['venue_id' => $oldVenue->id]);
        Passport::actingAs($admin, ['gpt:write']);

        $this->postJson(route('api.gpt.teams.venue.update', $team), [
            'venue_id' => $newVenue->id,
            'expected_current_venue_id' => $oldVenue->id,
        ])->assertOk()
            ->assertJsonPath('change.before.venue_id', $oldVenue->id)
            ->assertJsonPath('change.after.venue_id', $newVenue->id);

        $this->assertSame($newVenue->id, $team->refresh()->venue_id);
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
