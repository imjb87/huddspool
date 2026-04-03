<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\KnockoutType;
use App\Models\Fixture;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Notifications\FixtureResultOutstandingNotification;
use App\Notifications\KnockoutMatchReminderNotification;
use App\Notifications\KnockoutResultOutstandingNotification;
use App\Notifications\LeagueNightTonightNotification;
use App\Notifications\MatchNightStartedNotification;
use App\Notifications\TuesdayResultCatchupNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_league_night_command_notifies_players_and_team_admins_for_todays_fixture(): void
    {
        Carbon::setTestNow('2026-04-02 12:00:00');
        Notification::fake();

        [
            'fixture' => $fixture,
            'homeAdmin' => $homeAdmin,
            'homePlayer' => $homePlayer,
            'awayAdmin' => $awayAdmin,
            'awayPlayer' => $awayPlayer,
        ] = $this->createFixtureNotificationContext(today());

        $futureContext = $this->createFixtureNotificationContext(today()->addDay());

        $this->artisan('app:send-league-night-tonight-notifications')
            ->assertSuccessful();

        Notification::assertSentTo([$homeAdmin, $homePlayer, $awayAdmin, $awayPlayer], LeagueNightTonightNotification::class);
        Notification::assertNotSentTo($futureContext['homeAdmin'], LeagueNightTonightNotification::class);
        Notification::assertNotSentTo($futureContext['awayPlayer'], LeagueNightTonightNotification::class);
        Notification::assertSentTo(
            [$homeAdmin, $homePlayer],
            LeagueNightTonightNotification::class,
            function (LeagueNightTonightNotification $notification) use ($fixture, $homeAdmin): bool {
                $payload = $notification->toArray($homeAdmin);

                return $notification->fixture->is($fixture)
                    && $payload['action_url'] === route('fixture.show', $fixture)
                    && $payload['body'] === 'Get ready for match night. View your fixture, check your opponents, and make sure you\'re set for tonight.';
            }
        );
    }

    public function test_league_night_command_skips_users_without_an_email_address(): void
    {
        Carbon::setTestNow('2026-04-02 12:00:00');
        Notification::fake();

        [
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
        ] = $this->createFixtureNotificationContext(today());

        $homePlaceholder = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
            'email' => null,
            'is_admin' => false,
        ]);

        $awayPlaceholder = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::TeamAdmin->value,
            'email' => null,
            'is_admin' => false,
        ]);

        $this->artisan('app:send-league-night-tonight-notifications')
            ->assertSuccessful();

        Notification::assertNotSentTo($homePlaceholder, LeagueNightTonightNotification::class);
        Notification::assertNotSentTo($awayPlaceholder, LeagueNightTonightNotification::class);
    }

    public function test_outstanding_fixture_command_only_notifies_team_admins_and_deduplicates_reruns(): void
    {
        Carbon::setTestNow('2026-04-03 12:00:00');

        [
            'fixture' => $fixture,
            'homeAdmin' => $homeAdmin,
            'homePlayer' => $homePlayer,
            'awayAdmin' => $awayAdmin,
            'awayPlayer' => $awayPlayer,
        ] = $this->createFixtureNotificationContext(today()->subDay());

        Result::query()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $fixture->home_team_id,
            'home_team_name' => $fixture->homeTeam->name,
            'home_score' => 4,
            'away_team_id' => $fixture->away_team_id,
            'away_team_name' => $fixture->awayTeam->name,
            'away_score' => 3,
            'is_confirmed' => false,
        ]);

        $this->artisan('app:send-outstanding-fixture-notifications')
            ->assertSuccessful();

        $this->artisan('app:send-outstanding-fixture-notifications')
            ->assertSuccessful();

        Notification::assertSentTo(
            [$homeAdmin, $awayAdmin],
            FixtureResultOutstandingNotification::class,
            function (FixtureResultOutstandingNotification $notification, array $channels) use ($fixture): bool {
                return $channels === ['database', 'mail']
                    && $notification->fixture->is($fixture);
            }
        );
        $this->assertSame(1, $homeAdmin->notifications()->where('type', FixtureResultOutstandingNotification::class)->count());
        $this->assertSame(1, $awayAdmin->notifications()->where('type', FixtureResultOutstandingNotification::class)->count());
        $this->assertSame(0, $homePlayer->notifications()->where('type', FixtureResultOutstandingNotification::class)->count());
        $this->assertSame(0, $awayPlayer->notifications()->where('type', FixtureResultOutstandingNotification::class)->count());
    }

    public function test_match_night_started_command_only_notifies_team_admins_with_result_submission_link(): void
    {
        Carbon::setTestNow('2026-04-02 20:00:00');
        Notification::fake();

        [
            'fixture' => $fixture,
            'homeAdmin' => $homeAdmin,
            'homePlayer' => $homePlayer,
            'awayAdmin' => $awayAdmin,
            'awayPlayer' => $awayPlayer,
        ] = $this->createFixtureNotificationContext(today());

        $futureContext = $this->createFixtureNotificationContext(today()->addDay());

        $this->artisan('app:send-match-night-started-notifications')
            ->assertSuccessful();

        Notification::assertSentTo([$homeAdmin, $awayAdmin], MatchNightStartedNotification::class);
        Notification::assertNotSentTo($homePlayer, MatchNightStartedNotification::class);
        Notification::assertNotSentTo($awayPlayer, MatchNightStartedNotification::class);
        Notification::assertNotSentTo($futureContext['homeAdmin'], MatchNightStartedNotification::class);
        Notification::assertSentTo(
            $homeAdmin,
            MatchNightStartedNotification::class,
            function (MatchNightStartedNotification $notification) use ($fixture, $homeAdmin): bool {
                $payload = $notification->toArray($homeAdmin);

                return $notification->fixture->is($fixture)
                    && $payload['action_url'] === route('result.create', $fixture)
                    && $payload['body'] === 'Your match has started. Pick your players, keep things moving, and get ready to submit the result.';
            }
        );
    }

    public function test_tuesday_result_catchup_command_only_notifies_team_admins_for_previous_tuesday(): void
    {
        Carbon::setTestNow('2026-04-12 12:00:00');
        Notification::fake();

        [
            'fixture' => $fixture,
            'homeAdmin' => $homeAdmin,
            'homePlayer' => $homePlayer,
            'awayAdmin' => $awayAdmin,
            'awayPlayer' => $awayPlayer,
        ] = $this->createFixtureNotificationContext(today()->previous(Carbon::TUESDAY));

        Result::query()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $fixture->home_team_id,
            'home_team_name' => $fixture->homeTeam->name,
            'home_score' => 4,
            'away_team_id' => $fixture->away_team_id,
            'away_team_name' => $fixture->awayTeam->name,
            'away_score' => 3,
            'is_confirmed' => false,
        ]);

        $otherContext = $this->createFixtureNotificationContext(today()->subDay());

        $this->artisan('app:send-tuesday-result-catchup-notifications')
            ->assertSuccessful();

        Notification::assertSentTo(
            [$homeAdmin, $awayAdmin],
            TuesdayResultCatchupNotification::class,
            function (TuesdayResultCatchupNotification $notification, array $channels) use ($fixture): bool {
                return $channels === ['database', 'mail']
                    && $notification->fixture->is($fixture)
                    && $notification->toArray(new \stdClass)['action_url'] === route('result.create', $fixture);
            }
        );
        Notification::assertNotSentTo($homePlayer, TuesdayResultCatchupNotification::class);
        Notification::assertNotSentTo($awayPlayer, TuesdayResultCatchupNotification::class);
        Notification::assertNotSentTo($otherContext['homeAdmin'], TuesdayResultCatchupNotification::class);
        Notification::assertNotSentTo($otherContext['awayAdmin'], TuesdayResultCatchupNotification::class);
    }

    public function test_outstanding_knockout_command_notifies_participants_and_deduplicates_reruns(): void
    {
        Carbon::setTestNow('2026-04-03 12:00:00');

        $season = Season::factory()->create(['is_open' => true]);
        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Ready Cup',
            'type' => KnockoutType::Singles,
            'best_of' => 5,
        ]);

        $round = $knockout->rounds()->create([
            'name' => 'Quarter-finals',
            'position' => 1,
            'scheduled_for' => today()->subDay()->setTime(20, 0),
            'is_visible' => true,
        ]);

        $homePlayer = User::factory()->create(['name' => 'Knockout Home']);
        $awayPlayer = User::factory()->create(['name' => 'Knockout Away']);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $homePlayer->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $awayPlayer->id,
        ]);

        $match = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 5,
            'starts_at' => today()->subDay()->setTime(20, 0),
        ]);

        $this->artisan('app:send-outstanding-knockout-notifications')
            ->assertSuccessful();

        $this->artisan('app:send-outstanding-knockout-notifications')
            ->assertSuccessful();

        $homeNotification = $homePlayer->notifications()
            ->where('type', KnockoutResultOutstandingNotification::class)
            ->first();
        $awayNotification = $awayPlayer->notifications()
            ->where('type', KnockoutResultOutstandingNotification::class)
            ->first();

        $this->assertNotNull($homeNotification);
        $this->assertNotNull($awayNotification);
        $this->assertSame(1, $homePlayer->notifications()->where('type', KnockoutResultOutstandingNotification::class)->count());
        $this->assertSame(1, $awayPlayer->notifications()->where('type', KnockoutResultOutstandingNotification::class)->count());
        $this->assertSame(
            $match->id,
            data_get($homeNotification?->data, 'knockout_match_id'),
        );
        $this->assertSame(
            route('knockout.matches.submit', $match),
            data_get($homeNotification?->data, 'action_url'),
        );
    }

    public function test_knockout_match_reminder_command_notifies_everyone_involved_for_team_knockouts(): void
    {
        Carbon::setTestNow('2026-04-03 12:00:00');
        Notification::fake();

        [
            'knockout' => $knockout,
            'homeAdmin' => $homeAdmin,
            'homePlayer' => $homePlayer,
            'awayAdmin' => $awayAdmin,
            'awayPlayer' => $awayPlayer,
        ] = $this->createTeamKnockoutNotificationContext(today()->setTime(20, 0));

        $round = $knockout->rounds()->create([
            'name' => 'Semi-finals',
            'position' => 1,
            'scheduled_for' => today()->setTime(20, 0),
            'is_visible' => true,
        ]);

        $match = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $knockout->participants()->where('team_id', $homeAdmin->team_id)->value('id'),
            'away_participant_id' => $knockout->participants()->where('team_id', $awayAdmin->team_id)->value('id'),
            'starts_at' => today()->setTime(20, 0),
        ]);

        $this->artisan('app:send-knockout-match-reminder-notifications')
            ->assertSuccessful();

        Notification::assertSentTo(
            [$homeAdmin, $homePlayer, $awayAdmin, $awayPlayer],
            KnockoutMatchReminderNotification::class,
            function (KnockoutMatchReminderNotification $notification, array $channels) use ($match): bool {
                return $channels === ['database', 'mail']
                    && $notification->match->is($match);
            }
        );
    }

    public function test_outstanding_team_knockout_command_only_notifies_team_admins(): void
    {
        Carbon::setTestNow('2026-04-03 12:00:00');
        Notification::fake();

        [
            'knockout' => $knockout,
            'homeAdmin' => $homeAdmin,
            'homePlayer' => $homePlayer,
            'awayAdmin' => $awayAdmin,
            'awayPlayer' => $awayPlayer,
        ] = $this->createTeamKnockoutNotificationContext(today()->subDay()->setTime(20, 0));

        $round = $knockout->rounds()->create([
            'name' => 'Semi-finals',
            'position' => 1,
            'scheduled_for' => today()->subDay()->setTime(20, 0),
            'is_visible' => true,
        ]);

        $match = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $knockout->participants()->where('team_id', $homeAdmin->team_id)->value('id'),
            'away_participant_id' => $knockout->participants()->where('team_id', $awayAdmin->team_id)->value('id'),
            'starts_at' => today()->subDay()->setTime(20, 0),
        ]);

        $this->artisan('app:send-outstanding-knockout-notifications')
            ->assertSuccessful();

        Notification::assertSentTo(
            [$homeAdmin, $awayAdmin],
            KnockoutResultOutstandingNotification::class,
            function (KnockoutResultOutstandingNotification $notification, array $channels) use ($match): bool {
                return $channels === ['database', 'mail']
                    && $notification->match->is($match);
            }
        );
        Notification::assertNotSentTo($homePlayer, KnockoutResultOutstandingNotification::class);
        Notification::assertNotSentTo($awayPlayer, KnockoutResultOutstandingNotification::class);
    }

    /**
     * @return array{
     *     fixture: Fixture,
     *     homeTeam: Team,
     *     awayTeam: Team,
     *     homeAdmin: User,
     *     homePlayer: User,
     *     awayAdmin: User,
     *     awayPlayer: User
     * }
     */
    private function createFixtureNotificationContext(Carbon $fixtureDate): array
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
            'fixture_date' => $fixtureDate,
        ]);

        $homeAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::TeamAdmin->value,
            'is_admin' => false,
        ]);
        $homePlayer = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
            'is_admin' => false,
        ]);

        $awayAdmin = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::TeamAdmin->value,
            'is_admin' => false,
        ]);
        $awayPlayer = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::Player->value,
            'is_admin' => false,
        ]);

        return compact('fixture', 'homeTeam', 'awayTeam', 'homeAdmin', 'homePlayer', 'awayAdmin', 'awayPlayer');
    }

    /**
     * @return array{
     *     knockout: Knockout,
     *     homeTeam: Team,
     *     awayTeam: Team,
     *     homeAdmin: User,
     *     homePlayer: User,
     *     awayAdmin: User,
     *     awayPlayer: User
     * }
     */
    private function createTeamKnockoutNotificationContext(Carbon $scheduledFor): array
    {
        $season = Season::factory()->create(['is_open' => true]);
        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $homeAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::TeamAdmin->value,
            'is_admin' => false,
        ]);
        $homePlayer = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
            'is_admin' => false,
        ]);

        $awayAdmin = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::TeamAdmin->value,
            'is_admin' => false,
        ]);
        $awayPlayer = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::Player->value,
            'is_admin' => false,
        ]);

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team Cup',
            'type' => KnockoutType::Team,
            'published_at' => $scheduledFor->copy()->subWeek(),
        ]);

        $knockout->participants()->create([
            'team_id' => $homeTeam->id,
        ]);

        $knockout->participants()->create([
            'team_id' => $awayTeam->id,
        ]);

        return compact('knockout', 'homeTeam', 'awayTeam', 'homeAdmin', 'homePlayer', 'awayAdmin', 'awayPlayer');
    }
}
