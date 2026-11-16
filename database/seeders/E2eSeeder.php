<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Support\SiteAuthorization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class E2eSeeder extends Seeder
{
    public function run(): void
    {
        SiteAuthorization::ensureRolesAndPermissionsExist();

        $fixtureDate = now()->subDay()->toDateString();
        $season = Season::create([
            'name' => 'E2E Test Season',
            'slug' => 'e2e-test-season',
            'dates' => [$fixtureDate],
            'is_open' => true,
        ]);

        $ruleset = new Ruleset;
        $ruleset->name = 'E2E Test Ruleset';
        $ruleset->slug = 'e2e-test-ruleset';
        $ruleset->save();

        $section = Section::create([
            'name' => 'E2E Test Section',
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $venue = Venue::create([
            'name' => 'E2E Test Venue',
            'address' => 'E2E Test Address',
        ]);

        $homeTeam = Team::create([
            'name' => 'E2E Home Team',
            'shortname' => 'E2E-H',
            'venue_id' => $venue->id,
        ]);

        $awayTeam = Team::create([
            'name' => 'E2E Away Team',
            'shortname' => 'E2E-A',
            'venue_id' => $venue->id,
        ]);

        $section->teams()->sync([
            $homeTeam->id => ['sort' => 1],
            $awayTeam->id => ['sort' => 2],
        ]);

        $homeAdmin = $this->createTeamAdmin(
            team: $homeTeam,
            name: 'E2E Home Admin',
            email: 'e2e-home@example.test',
        );
        $awayAdmin = $this->createTeamAdmin(
            team: $awayTeam,
            name: 'E2E Away Admin',
            email: 'e2e-away@example.test',
        );

        $homeTeam->update(['captain_id' => $homeAdmin->id]);
        $awayTeam->update(['captain_id' => $awayAdmin->id]);

        $this->createPlayer($homeTeam, 'E2E Home Player 2', 'e2e-home-player-2@example.test');
        $this->createPlayer($homeTeam, 'E2E Home Player 3', 'e2e-home-player-3@example.test');
        $this->createPlayer($awayTeam, 'E2E Away Player 2', 'e2e-away-player-2@example.test');
        $this->createPlayer($awayTeam, 'E2E Away Player 3', 'e2e-away-player-3@example.test');

        $scoreFixture = $this->createFixture(
            season: $season,
            section: $section,
            ruleset: $ruleset,
            venue: $venue,
            homeTeam: $homeTeam,
            awayTeam: $awayTeam,
            fixtureDate: $fixtureDate,
            week: 1,
        );

        $collaborationFixture = $this->createFixture(
            season: $season,
            section: $section,
            ruleset: $ruleset,
            venue: $venue,
            homeTeam: $homeTeam,
            awayTeam: $awayTeam,
            fixtureDate: $fixtureDate,
            week: 2,
        );

        $manifestPath = storage_path('app/e2e-fixtures.json');
        File::ensureDirectoryExists(dirname($manifestPath));
        File::put($manifestPath, json_encode([
            'score_fixture_id' => $scoreFixture->id,
            'collaboration_fixture_id' => $collaborationFixture->id,
            'accounts' => [
                'home' => $homeAdmin->email,
                'away' => $awayAdmin->email,
                'password' => 'password',
            ],
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    private function createTeamAdmin(Team $team, string $name, string $email): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'team_id' => $team->id,
        ]);

        SiteAuthorization::assignRole($user, RoleName::TeamAdmin);

        return $user;
    }

    private function createPlayer(Team $team, string $name, string $email): User
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'team_id' => $team->id,
        ]);
    }

    private function createFixture(
        Season $season,
        Section $section,
        Ruleset $ruleset,
        Venue $venue,
        Team $homeTeam,
        Team $awayTeam,
        string $fixtureDate,
        int $week,
    ): Fixture {
        return Fixture::create([
            'week' => $week,
            'fixture_date' => $fixtureDate,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'season_id' => $season->id,
            'section_id' => $section->id,
            'venue_id' => $venue->id,
            'ruleset_id' => $ruleset->id,
        ]);
    }
}
