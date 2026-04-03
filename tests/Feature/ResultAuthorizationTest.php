<?php

namespace Tests\Feature;

use App\Livewire\ResultForm;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ResultAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_receives_forbidden_when_opening_result_create_route(): void
    {
        ['fixture' => $fixture] = $this->createResultFixtureContext(now()->subDay());

        $this->get(route('result.create', $fixture))
            ->assertForbidden();
    }

    public function test_team_admin_on_fixture_team_can_open_result_create_route(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam] = $this->createResultFixtureContext(now()->subDay());

        $teamAdmin = $this->createUserForTeam($homeTeam, role: 2);

        $this->actingAs($teamAdmin)
            ->get(route('result.create', $fixture))
            ->assertOk()
            ->assertSeeLivewire(ResultForm::class);
    }

    public function test_team_captain_on_fixture_team_receives_forbidden_when_opening_result_create_route(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam] = $this->createResultFixtureContext(now()->subDay());

        $captain = $this->createUserForTeam($homeTeam);
        $homeTeam->update(['captain_id' => $captain->id]);

        $this->actingAs($captain)
            ->get(route('result.create', $fixture))
            ->assertForbidden();
    }

    public function test_team_captain_on_fixture_team_cannot_resume_an_in_progress_result_without_submit_permission(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam, 'awayTeam' => $awayTeam, 'section' => $section, 'ruleset' => $ruleset] = $this->createResultFixtureContext(now()->subDay());

        $captain = $this->createUserForTeam($homeTeam);
        $homeTeam->update(['captain_id' => $captain->id]);

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => false,
        ]);

        $this->actingAs($captain)
            ->get(route('result.create', $fixture))
            ->assertForbidden();
    }

    public function test_regular_player_on_fixture_team_receives_forbidden_when_opening_result_create_route(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam] = $this->createResultFixtureContext(now()->subDay());

        $player = $this->createUserForTeam($homeTeam);

        $this->actingAs($player)
            ->get(route('result.create', $fixture))
            ->assertForbidden();
    }

    public function test_future_fixture_returns_not_found_on_result_create_route(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam] = $this->createResultFixtureContext(now()->addDay());

        $teamAdmin = $this->createUserForTeam($homeTeam, role: 2);

        $this->actingAs($teamAdmin)
            ->get(route('result.create', $fixture))
            ->assertNotFound();
    }

    public function test_placeholder_team_fixture_returns_not_found_on_result_create_route(): void
    {
        $placeholderTeam = Team::factory()->create(['name' => Team::BYE_NAME]);
        $awayTeam = Team::factory()->create();

        ['fixture' => $fixture] = $this->createResultFixtureContext(
            fixtureDate: now()->subDay(),
            homeTeam: $placeholderTeam,
            awayTeam: $awayTeam,
        );

        $teamAdmin = $this->createUserForTeam($awayTeam, role: 2);

        $this->actingAs($teamAdmin)
            ->get(route('result.create', $fixture))
            ->assertNotFound();
    }

    public function test_fixture_page_does_not_show_submit_result_entry_point_for_site_admin(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam] = $this->createResultFixtureContext(now()->subDay());

        $siteAdmin = $this->createUserForTeam($homeTeam, isAdmin: true);

        $this->actingAs($siteAdmin)
            ->get(route('fixture.show', $fixture))
            ->assertOk()
            ->assertDontSeeText('Result submission')
            ->assertDontSeeText('Submit result');
    }

    public function test_site_admin_on_fixture_team_can_open_result_create_route(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam] = $this->createResultFixtureContext(now()->subDay());

        $siteAdmin = $this->createUserForTeam($homeTeam, isAdmin: true);

        $this->actingAs($siteAdmin)
            ->get(route('result.create', $fixture))
            ->assertOk()
            ->assertSeeLivewire(ResultForm::class);
    }

    public function test_site_admin_not_on_fixture_team_can_open_result_create_route(): void
    {
        ['fixture' => $fixture] = $this->createResultFixtureContext(now()->subDay());

        $siteAdmin = User::factory()->create([
            'is_admin' => true,
        ]);
        $siteAdmin->assignRole('admin');

        $this->actingAs($siteAdmin)
            ->get(route('result.create', $fixture))
            ->assertOk()
            ->assertSeeLivewire(ResultForm::class);
    }

    public function test_site_admin_on_fixture_team_does_not_see_a_public_result_submission_link(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam, 'awayTeam' => $awayTeam, 'section' => $section, 'ruleset' => $ruleset] = $this->createResultFixtureContext(now()->subDay());

        $teamAdmin = $this->createUserForTeam($homeTeam, role: 2);
        $siteAdmin = $this->createUserForTeam($homeTeam, isAdmin: true);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 3,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 2,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $teamAdmin->id,
            'is_confirmed' => false,
        ]);

        $this->actingAs($siteAdmin)
            ->get(route('result.show', $result))
            ->assertOk()
            ->assertDontSeeText('Continue submitting result');
    }

    public function test_site_admin_not_on_fixture_team_does_not_see_a_public_result_submission_link_on_result_page(): void
    {
        ['fixture' => $fixture, 'homeTeam' => $homeTeam, 'awayTeam' => $awayTeam, 'section' => $section, 'ruleset' => $ruleset] = $this->createResultFixtureContext(now()->subDay());

        $teamAdmin = $this->createUserForTeam($homeTeam, role: 2);
        $siteAdmin = User::factory()->create([
            'is_admin' => true,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 4,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 3,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $teamAdmin->id,
            'is_confirmed' => false,
        ]);

        $this->actingAs($siteAdmin)
            ->get(route('result.show', $result))
            ->assertOk()
            ->assertDontSeeText('Continue submitting result');
    }

    /**
     * @return array{
     *     season: Season,
     *     ruleset: Ruleset,
     *     section: Section,
     *     homeTeam: Team,
     *     awayTeam: Team,
     *     fixture: Fixture
     * }
     */
    private function createResultFixtureContext(Carbon $fixtureDate, ?Team $homeTeam = null, ?Team $awayTeam = null): array
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $homeTeam ??= Team::factory()->create();
        $awayTeam ??= Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => $fixtureDate,
        ]);

        return compact('season', 'ruleset', 'section', 'homeTeam', 'awayTeam', 'fixture');
    }

    private function createUserForTeam(Team $team, int $role = 1, bool $isAdmin = false): User
    {
        return User::factory()->create([
            'team_id' => $team->id,
            'role' => $role,
            'is_admin' => $isAdmin,
        ]);
    }
}
