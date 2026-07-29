<?php

namespace Tests\Feature;

use App\Models\GptActionAudit;
use App\Models\News;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
