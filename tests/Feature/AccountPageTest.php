<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Enums\UserRole;
use App\KnockoutType;
use App\Livewire\Account\Show as AccountShow;
use App\Livewire\Player\FramesSection;
use App\Livewire\Team\FixturesSection as TeamFixturesSection;
use App\Livewire\Team\PlayersSection as TeamPlayersSection;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Notifications\LeagueResultSubmittedNotification;
use App\Support\SiteAuthorization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AccountPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_account_page(): void
    {
        $this->get(route('account.show'))
            ->assertRedirect(route('login'));
    }

    public function test_account_page_displays_an_unread_notification_badge_in_the_navigation(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($result));

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-mobile-notifications-toggle', false)
            ->assertSee('Open notifications menu', false)
            ->assertSee('data-mobile-notifications-drawer', false)
            ->assertSee('data-mobile-notifications-links', false);
    }

    public function test_opening_a_notification_marks_it_as_read_and_redirects_to_its_target(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($result));

        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)
            ->get(route('account.notifications.open', $notification->id))
            ->assertRedirect(route('result.show', $result));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $firstResult = Result::factory()->create();
        $secondResult = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($firstResult));
        $user->notify(new LeagueResultSubmittedNotification($secondResult));

        $this->actingAs($user)
            ->from(route('account.show'))
            ->post(route('account.notifications.read-all'))
            ->assertRedirect(route('account.show'));

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_notifications_summary_endpoint_returns_latest_notifications_and_unread_count(): void
    {
        $user = User::factory()->create();
        $firstResult = Result::factory()->create();
        $secondResult = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($firstResult));
        $user->notify(new LeagueResultSubmittedNotification($secondResult));

        $this->actingAs($user)
            ->getJson(route('account.notifications.summary'))
            ->assertOk()
            ->assertJsonPath('unread_count', 2)
            ->assertJsonCount(2, 'notifications')
            ->assertJsonPath('notifications.0.open_url', route('account.notifications.open', $user->notifications()->latest()->firstOrFail()->id));
    }

    public function test_user_can_mark_a_single_notification_as_read(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($result));

        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)
            ->from(route('account.show'))
            ->post(route('account.notifications.read', $notification->id))
            ->assertRedirect(route('account.show'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read_with_json_response(): void
    {
        $user = User::factory()->create();
        $firstResult = Result::factory()->create();
        $secondResult = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($firstResult));
        $user->notify(new LeagueResultSubmittedNotification($secondResult));

        $this->actingAs($user)
            ->postJson(route('account.notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_mark_a_single_notification_as_read_with_json_response(): void
    {
        $user = User::factory()->create();
        $result = Result::factory()->create();

        $user->notify(new LeagueResultSubmittedNotification($result));

        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)
            ->postJson(route('account.notifications.read', $notification->id))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_authenticated_user_can_view_account_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-account-page', false)
            ->assertSee('data-account-header', false)
            ->assertSee('data-account-nav', false)
            ->assertSee('data-account-profile-section', false)
            ->assertSee('col-span-full min-w-0 sm:col-span-1', false)
            ->assertSee('ui-link flex w-full max-w-full text-sm font-semibold', false)
            ->assertSee('ui-page-shell', false)
            ->assertSee('ui-button-primary', false)
            ->assertSee('ui-tab-strip-shell', false)
            ->assertSee('ui-tab-strip', false)
            ->assertSee('ui-card', false)
            ->assertSee('dark:bg-neutral-950', false)
            ->assertSee('dark:border-neutral-800', false)
            ->assertSee('dark:bg-neutral-900/75', false)
            ->assertSee('dark:ring-neutral-800/80', false)
            ->assertSee('dark:text-gray-100', false)
            ->assertSee('href="'.route('player.show', $user).'"', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertDontSee('href="/account/notifications"', false)
            ->assertSeeText('Email address');
    }

    public function test_account_page_displays_push_notification_settings_in_profile_section(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-account-profile-section', false)
            ->assertSee('data-account-push-settings', false)
            ->assertSee('role="switch"', false)
            ->assertSeeText('Push notifications');
    }

    public function test_account_page_includes_the_one_time_native_push_prompt_for_users_who_have_not_been_asked(): void
    {
        config()->set('services.web_push.public_key', 'public-key');
        config()->set('services.web_push.private_key', 'private-key');
        config()->set('services.web_push.subject', 'mailto:notifications@example.com');

        $user = User::factory()->create([
            'push_prompted_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-auto-push-permission-prompt', false);
    }

    public function test_account_page_does_not_include_the_one_time_native_push_prompt_after_the_user_has_been_asked(): void
    {
        config()->set('services.web_push.public_key', 'public-key');
        config()->set('services.web_push.private_key', 'private-key');
        config()->set('services.web_push.subject', 'mailto:notifications@example.com');

        $user = User::factory()->create([
            'push_prompted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-auto-push-permission-prompt', false);
    }

    public function test_account_page_does_not_include_the_one_time_native_push_prompt_for_users_with_push_enabled(): void
    {
        config()->set('services.web_push.public_key', 'public-key');
        config()->set('services.web_push.private_key', 'private-key');
        config()->set('services.web_push.subject', 'mailto:notifications@example.com');

        $user = User::factory()->create([
            'push_prompted_at' => null,
        ]);

        $user->pushSubscriptions()->create([
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-auto-push-permission-prompt', false);
    }

    public function test_account_page_does_not_render_a_notifications_tab(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('href="/account/notifications"', false);
    }

    public function test_team_account_page_does_not_render_a_notifications_tab(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $this->actingAs($user)
            ->get(route('account.team'))
            ->assertOk()
            ->assertDontSee('href="/account/notifications"', false);
    }

    public function test_account_page_does_not_list_connected_push_devices(): void
    {
        $user = User::factory()->create();
        $user->pushSubscriptions()->create([
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
            'device_label' => 'Safari on iPhone',
            'browser' => 'Safari',
            'platform' => 'iPhone',
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSeeText('Connected devices')
            ->assertDontSeeText('Safari on iPhone');
    }

    public function test_user_can_upload_and_delete_avatar_from_account_page(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'avatar_path' => 'avatars/old-avatar.jpg',
        ]);

        Storage::disk('public')->put('avatars/old-avatar.jpg', 'old-avatar');

        Livewire::actingAs($user)
            ->test(AccountShow::class)
            ->set('avatarUpload', UploadedFile::fake()->image('avatar.jpg'))
            ->call('saveProfile');

        $user->refresh();
        $uploadedMedia = $user->getFirstMedia('avatars');

        $this->assertNotNull($uploadedMedia);
        $this->assertNull($user->avatar_path);
        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
        Storage::disk('public')->assertExists($uploadedMedia->getPathRelativeToRoot());

        Livewire::actingAs($user)
            ->test(AccountShow::class)
            ->call('clearAvatar')
            ->call('saveProfile');

        $this->assertFalse($user->fresh()->hasMedia('avatars'));
        Storage::disk('public')->assertMissing($uploadedMedia->getPathRelativeToRoot());
    }

    public function test_user_can_update_contact_details_from_account_page(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'telephone' => '01234 567890',
        ]);

        Livewire::actingAs($user)
            ->test(AccountShow::class)
            ->set('email', 'new@example.com')
            ->set('telephone', '09876 543210')
            ->call('saveProfile');

        $user->refresh();

        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('09876 543210', $user->telephone);
    }

    public function test_team_admin_sees_team_nav_link_on_account_page(): void
    {
        $team = Team::factory()->create();

        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('href="'.route('account.team').'"', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertSeeText('Team')
            ->assertDontSee('data-account-team-section', false);
    }

    public function test_team_admin_does_not_see_result_submission_prompt_on_account_page_when_fixture_is_due(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create(['shortname' => 'TEAM']);
        $opponentTeam = Team::factory()->create(['shortname' => 'OPP']);
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
            'fixture_date' => now(),
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-account-result-submission-prompt', false)
            ->assertDontSeeText($team->name.' vs '.$opponentTeam->name)
            ->assertDontSee(route('result.create', $fixture), false);
    }

    public function test_team_admin_does_not_see_result_submission_prompt_for_multiple_outstanding_results(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create(['name' => 'Home']);
        $firstOpponent = Team::factory()->create(['name' => 'First Opponent']);
        $secondOpponent = Team::factory()->create(['name' => 'Second Opponent']);
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $firstFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $firstOpponent->id,
            'fixture_date' => now()->subDays(2),
        ]);

        $secondFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $secondOpponent->id,
            'away_team_id' => $team->id,
            'fixture_date' => now()->subDay(),
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-account-result-submission-prompt', false)
            ->assertDontSeeText('League matches')
            ->assertDontSeeText('Home vs First Opponent')
            ->assertDontSeeText('Second Opponent vs Home')
            ->assertDontSee(route('result.create', $firstFixture), false)
            ->assertDontSee(route('result.create', $secondFixture), false);
    }

    public function test_account_page_does_not_render_due_knockout_result_prompt_for_players(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $user = User::factory()->create(['role' => UserRole::Player->value]);

        $singlesKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Singles Cup',
            'type' => KnockoutType::Singles,
        ]);

        $singlesRound = KnockoutRound::query()->create([
            'knockout_id' => $singlesKnockout->id,
            'name' => 'Quarter-finals',
            'position' => 1,
            'is_visible' => true,
        ]);

        $singlesHomeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $singlesKnockout->id,
            'player_one_id' => $user->id,
        ]);

        $singlesAwayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $singlesKnockout->id,
            'player_one_id' => User::factory()->create()->id,
        ]);

        $singlesMatch = KnockoutMatch::query()->create([
            'knockout_id' => $singlesKnockout->id,
            'knockout_round_id' => $singlesRound->id,
            'position' => 1,
            'home_participant_id' => $singlesHomeParticipant->id,
            'away_participant_id' => $singlesAwayParticipant->id,
            'best_of' => 5,
            'starts_at' => now()->subDay(),
        ]);

        $doublesKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Doubles Shield',
            'type' => KnockoutType::Doubles,
        ]);

        $doublesRound = KnockoutRound::query()->create([
            'knockout_id' => $doublesKnockout->id,
            'name' => 'Round 1',
            'position' => 1,
            'is_visible' => true,
        ]);

        $doublesHomeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $doublesKnockout->id,
            'player_one_id' => $user->id,
            'player_two_id' => User::factory()->create()->id,
        ]);

        $doublesAwayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $doublesKnockout->id,
            'player_one_id' => User::factory()->create()->id,
            'player_two_id' => User::factory()->create()->id,
        ]);

        $doublesMatch = KnockoutMatch::query()->create([
            'knockout_id' => $doublesKnockout->id,
            'knockout_round_id' => $doublesRound->id,
            'position' => 1,
            'home_participant_id' => $doublesHomeParticipant->id,
            'away_participant_id' => $doublesAwayParticipant->id,
            'best_of' => 7,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-account-result-submission-prompt', false)
            ->assertDontSeeText('2 knockout results are ready to submit.');
    }

    public function test_account_page_still_does_not_render_a_result_prompt_before_knockouts_are_due(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $user = User::factory()->create(['role' => UserRole::Player->value]);

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Singles Cup',
            'type' => KnockoutType::Singles,
        ]);

        $round = KnockoutRound::query()->create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter-finals',
            'position' => 1,
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $user->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => User::factory()->create()->id,
        ]);

        $futureMatch = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 5,
            'starts_at' => now()->addDay(),
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-account-result-submission-prompt', false)
            ->assertSee(route('knockout.matches.submit', $futureMatch), false);
    }

    public function test_account_page_does_not_render_combined_league_and_knockout_result_prompt(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create(['name' => 'Home']);
        $opponentTeam = Team::factory()->create(['name' => 'Opposition']);
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);
        $team->update(['captain_id' => $teamAdmin->id]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
            'fixture_date' => now(),
        ]);

        $teamKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team KO',
            'type' => KnockoutType::Team,
        ]);

        $teamRound = KnockoutRound::query()->create([
            'knockout_id' => $teamKnockout->id,
            'name' => 'Semi-finals',
            'position' => 1,
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $team->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $opponentTeam->id,
        ]);

        $teamMatch = KnockoutMatch::query()->create([
            'knockout_id' => $teamKnockout->id,
            'knockout_round_id' => $teamRound->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-account-result-submission-prompt', false)
            ->assertDontSeeText('1 team result and 1 knockout result are ready to submit.')
            ->assertDontSeeText('Home vs Opposition')
            ->assertDontSeeText('Team KO / Semi-finals')
            ->assertDontSee(route('result.create', $fixture), false)
            ->assertDontSee(route('knockout.matches.submit', $teamMatch), false);
    }

    public function test_admin_is_not_prompted_for_unrelated_knockout_results_on_account_page(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $adminTeam = Team::factory()->create();
        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();
        $admin = User::factory()->create([
            'team_id' => $adminTeam->id,
            'role' => UserRole::Player->value,
        ]);
        SiteAuthorization::assignRole($admin, RoleName::Admin);

        $teamKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team KO',
            'type' => KnockoutType::Team,
        ]);

        $teamRound = KnockoutRound::query()->create([
            'knockout_id' => $teamKnockout->id,
            'name' => 'Semi-finals',
            'position' => 1,
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $team->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $opponentTeam->id,
        ]);

        $match = KnockoutMatch::query()->create([
            'knockout_id' => $teamKnockout->id,
            'knockout_round_id' => $teamRound->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('data-account-result-submission-prompt', false);

        $this->actingAs($admin)
            ->get(route('knockout.matches.submit', $match))
            ->assertOk();
    }

    public function test_captain_does_not_see_team_nav_link_on_account_page(): void
    {
        $homeTeam = Team::factory()->create();

        $captain = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
        ]);

        $homeTeam->update(['captain_id' => $captain->id]);

        $member = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
            'name' => 'Squad Player',
        ]);

        $this->actingAs($captain)
            ->get(route('account.show'))
            ->assertOk()
            ->assertDontSee('href="'.route('account.team').'"', false)
            ->assertDontSee('data-account-team-section', false);
    }

    public function test_team_admin_can_view_team_account_page(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);
        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
            'name' => 'Team Admin',
        ]);
        $team->update(['captain_id' => $teamAdmin->id]);
        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponentTeam->id, ['sort' => 2]);

        $dueFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $futureFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $opponentTeam->id,
            'away_team_id' => $team->id,
            'fixture_date' => now()->addDay(),
        ]);

        $continueFixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponentTeam->id,
            'fixture_date' => now()->subDays(2),
        ]);

        Result::factory()->create([
            'fixture_id' => $continueFixture->id,
            'home_team_id' => $team->id,
            'home_team_name' => $team->name,
            'home_score' => 0,
            'away_team_id' => $opponentTeam->id,
            'away_team_name' => $opponentTeam->name,
            'away_score' => 0,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'is_confirmed' => false,
        ]);

        $teamKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team KO',
            'type' => KnockoutType::Team,
        ]);

        $teamRound = KnockoutRound::query()->create([
            'knockout_id' => $teamKnockout->id,
            'name' => 'Semi-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $teamHomeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $team->id,
        ]);

        $teamAwayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $opponentTeam->id,
        ]);

        $completedTeamMatch = KnockoutMatch::query()->create([
            'knockout_id' => $teamKnockout->id,
            'knockout_round_id' => $teamRound->id,
            'position' => 1,
            'home_participant_id' => $teamHomeParticipant->id,
            'away_participant_id' => $teamAwayParticipant->id,
            'home_score' => 6,
            'away_score' => 2,
            'winner_participant_id' => $teamHomeParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDays(2),
        ]);

        $pendingTeamMatch = KnockoutMatch::query()->create([
            'knockout_id' => $teamKnockout->id,
            'knockout_round_id' => $teamRound->id,
            'position' => 2,
            'home_participant_id' => $teamHomeParticipant->id,
            'away_participant_id' => $teamAwayParticipant->id,
            'best_of' => 7,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($teamAdmin)
            ->get(route('account.team'))
            ->assertOk()
            ->assertSee('data-account-team-page', false)
            ->assertSee('dark:bg-neutral-950', false)
            ->assertSee('dark:border-neutral-800/80', false)
            ->assertSee('dark:bg-neutral-900/75', false)
            ->assertSee('dark:text-gray-100', false)
            ->assertSee('data-account-team-info-section', false)
            ->assertSee('data-account-team-section', false)
            ->assertSee('data-account-team-member-stats', false)
            ->assertSee('data-player-stats-line', false)
            ->assertSee('data-account-team-fixtures-section', false)
            ->assertSee('data-account-team-knockout-section', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertSeeText('Team members')
            ->assertSeeText('Fixtures')
            ->assertSeeText('Team knockouts')
            ->assertSeeText(UserRole::labelFor($teamAdmin->role))
            ->assertSeeText($team->name)
            ->assertSeeText('Premier Division')
            ->assertSeeText('Team Admin')
            ->assertSeeText($team->name)
            ->assertSeeText($opponentTeam->name)
            ->assertSee('sm:hidden', false)
            ->assertSee('sm:block', false)
            ->assertSeeText($teamKnockout->name)
            ->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]).'"', false)
            ->assertSee('href="'.route('venue.show', $team->venue).'"', false)
            ->assertSee('href="'.route('player.show', $teamAdmin).'"', false)
            ->assertSee(route('result.create', $dueFixture), false)
            ->assertSee(route('result.create', $continueFixture), false)
            ->assertDontSee(route('result.create', $futureFixture), false)
            ->assertSee('data-account-team-fixture-ready', false)
            ->assertDontSeeText('Submit result')
            ->assertDontSee('href="'.route('fixture.show', $dueFixture).'"', false)
            ->assertDontSee('href="'.route('result.show', $continueFixture->result).'"', false)
            ->assertSee('from-gray-600 via-gray-500 to-gray-400', false)
            ->assertSee(route('knockout.show', $teamKnockout), false)
            ->assertSee(route('knockout.matches.submit', $pendingTeamMatch), false)
            ->assertDontSee(route('knockout.matches.submit', $completedTeamMatch), false);
    }

    public function test_regular_player_can_not_view_team_account_page(): void
    {
        $team = Team::factory()->create();
        $player = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::Player->value,
        ]);

        $this->actingAs($player)
            ->get(route('account.team'))
            ->assertForbidden();
    }

    public function test_team_account_page_paginates_team_members(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create();
        $opponent = Team::factory()->create();
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
            'name' => 'Captain Account',
        ]);

        $team->update(['captain_id' => $teamAdmin->id]);
        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponent->id, ['sort' => 2]);

        foreach (range(1, 7) as $index) {
            User::factory()->create([
                'team_id' => $team->id,
                'role' => UserRole::Player->value,
                'name' => sprintf('Account Team Player %02d', $index),
            ]);
        }

        $this->actingAs($teamAdmin)
            ->get(route('account.team'))
            ->assertOk()
            ->assertSee('data-account-team-section', false)
            ->assertSee('data-account-team-players-controls', false)
            ->assertSeeText('Page 1')
            ->assertSeeText('Account Team Player 07')
            ->assertSeeText('Account Team Player 03')
            ->assertDontSeeText('Account Team Player 02');

        Livewire::actingAs($teamAdmin)
            ->test(TeamPlayersSection::class, [
                'team' => $team,
                'section' => $section,
                'forAccount' => true,
            ])
            ->assertSeeText('Page 1')
            ->assertDontSeeText('Account Team Player 02')
            ->call('nextPage')
            ->assertSeeText('Page 2')
            ->assertSeeText('Account Team Player 02')
            ->assertSeeText('Account Team Player 01')
            ->assertDontSeeText('Account Team Player 03');
    }

    public function test_team_account_page_defaults_fixtures_to_the_page_for_the_current_week(): void
    {
        $today = now();

        $season = Season::factory()->create([
            'is_open' => true,
            'dates' => [
                $today->copy()->subWeeks(5)->toDateString(),
                $today->copy()->subWeeks(4)->toDateString(),
                $today->copy()->subWeeks(3)->toDateString(),
                $today->copy()->subWeeks(2)->toDateString(),
                $today->copy()->subWeek()->toDateString(),
                $today->copy()->toDateString(),
                $today->copy()->addWeek()->toDateString(),
                $today->copy()->addWeeks(2)->toDateString(),
            ],
        ]);

        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create(['name' => 'Account Fixtures Team']);
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $section->teams()->attach($team->id, ['sort' => 1]);

        foreach (range(1, 8) as $week) {
            $opponent = Team::factory()->create(['name' => sprintf('Account Opponent Week %02d', $week)]);
            $section->teams()->attach($opponent->id, ['sort' => $week + 1]);

            Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $team->id,
                'away_team_id' => $opponent->id,
                'week' => $week,
                'fixture_date' => $today->copy()->addWeeks($week - 6),
            ]);
        }

        $this->actingAs($teamAdmin)
            ->get(route('account.team'))
            ->assertOk()
            ->assertSee('data-account-team-fixtures-section', false)
            ->assertSee('data-account-team-fixtures-controls', false)
            ->assertSeeText('Page 2')
            ->assertSeeText('Account Opponent Week 06')
            ->assertSeeText('Account Opponent Week 08')
            ->assertDontSeeText('Account Opponent Week 01');

        Livewire::actingAs($teamAdmin)
            ->test(TeamFixturesSection::class, [
                'team' => $team,
                'section' => $section,
                'forAccount' => true,
            ])
            ->assertSeeText('Page 2')
            ->assertSeeText('Account Opponent Week 06')
            ->assertDontSeeText('Account Opponent Week 01');
    }

    public function test_team_account_page_defaults_fixtures_to_the_previous_scheduled_week_during_a_week_off(): void
    {
        $today = now();

        $season = Season::factory()->create([
            'is_open' => true,
            'dates' => collect(range(1, 8))
                ->map(fn (int $week): string => $today->copy()->addWeeks(match ($week) {
                    1 => -6,
                    2 => -5,
                    3 => -4,
                    4 => -3,
                    5 => -2,
                    6 => -1,
                    7 => 1,
                    8 => 2,
                })->toDateString())
                ->all(),
        ]);

        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create(['name' => 'Account Fixtures Team']);
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $section->teams()->attach($team->id, ['sort' => 1]);

        foreach (range(1, 8) as $week) {
            $opponent = Team::factory()->create(['name' => sprintf('Account Opponent Week %02d', $week)]);
            $section->teams()->attach($opponent->id, ['sort' => $week + 1]);

            Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $team->id,
                'away_team_id' => $opponent->id,
                'week' => $week,
                'fixture_date' => $season->dates[$week - 1],
            ]);
        }

        Livewire::actingAs($teamAdmin)
            ->test(TeamFixturesSection::class, [
                'team' => $team,
                'section' => $section,
                'forAccount' => true,
            ])
            ->assertSeeText('Page 2')
            ->assertSeeText('Account Opponent Week 06')
            ->assertDontSeeText('Account Opponent Week 01');
    }

    public function test_team_account_fixtures_use_shortnames_on_mobile(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        $team = Team::factory()->create([
            'name' => 'Account Team Long Name',
            'shortname' => 'ATL',
        ]);
        $opponent = Team::factory()->create([
            'name' => 'Account Opponent Long Name',
            'shortname' => 'AOL',
        ]);
        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $section->teams()->attach($team->id, ['sort' => 1]);
        $section->teams()->attach($opponent->id, ['sort' => 2]);

        Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $team->id,
            'away_team_id' => $opponent->id,
            'fixture_date' => now(),
        ]);

        Livewire::actingAs($teamAdmin)
            ->test(TeamFixturesSection::class, [
                'team' => $team,
                'section' => $section,
                'forAccount' => true,
            ])
            ->assertSeeText('ATL')
            ->assertSeeText('AOL')
            ->assertSeeText('Account Team Long Name')
            ->assertSeeText('Account Opponent Long Name')
            ->assertSee('sm:hidden', false)
            ->assertSee('sm:block', false);
    }

    public function test_account_page_shows_player_history_sections(): void
    {
        $season = Season::factory()->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

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

        $player = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
        ]);

        $opponent = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => UserRole::Player->value,
        ]);

        $result = Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'home_score' => 1,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'away_score' => 0,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'submitted_by' => $player->id,
        ]);

        Frame::query()->create([
            'result_id' => $result->id,
            'home_player_id' => $player->id,
            'home_score' => 1,
            'away_player_id' => $opponent->id,
            'away_score' => 0,
        ]);

        $this->actingAs($player)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-account-frames-section', false)
            ->assertSee('data-account-history-section', false)
            ->assertSeeText('History')
            ->assertSeeText($season->name)
            ->assertSeeText($homeTeam->name)
            ->assertSeeText('Premier Division')
            ->assertSeeText('Played')
            ->assertSeeText('Won')
            ->assertSeeText('Lost')
            ->assertSeeText($opponent->name)
            ->assertSeeText($awayTeam->name);
    }

    public function test_account_page_paginates_frames_section(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Premier Division',
        ]);

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $player = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::Player->value,
        ]);

        foreach (range(1, 21) as $index) {
            $fixture = Fixture::factory()->create([
                'season_id' => $season->id,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'fixture_date' => now()->subDays(21 - $index),
            ]);

            $result = Result::factory()->create([
                'fixture_id' => $fixture->id,
                'home_team_id' => $homeTeam->id,
                'home_team_name' => $homeTeam->name,
                'home_score' => 1,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'away_score' => 0,
                'section_id' => $section->id,
                'ruleset_id' => $ruleset->id,
                'submitted_by' => $player->id,
            ]);

            $opponent = User::factory()->create([
                'name' => sprintf('Account Opponent %02d', $index),
                'team_id' => $awayTeam->id,
                'role' => UserRole::Player->value,
            ]);

            Frame::query()->create([
                'result_id' => $result->id,
                'home_player_id' => $player->id,
                'home_score' => 1,
                'away_player_id' => $opponent->id,
                'away_score' => 0,
            ]);
        }

        $this->actingAs($player)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-account-frames-controls', false)
            ->assertSeeText('Page 1')
            ->assertSeeText('Account Opponent 21')
            ->assertSeeText('Account Opponent 17')
            ->assertDontSeeText('Account Opponent 16')
            ->assertDontSeeText('Account Opponent 01');

        Livewire::actingAs($player)
            ->test(FramesSection::class, [
                'player' => $player,
                'section' => $section,
                'forAccount' => true,
            ])
            ->assertSeeText('Page 1')
            ->assertDontSeeText('Account Opponent 01')
            ->call('nextPage')
            ->assertSeeText('Page 2')
            ->assertSeeText('Account Opponent 16')
            ->assertDontSeeText('Account Opponent 17')
            ->assertDontSeeText('Account Opponent 11');
    }

    public function test_account_page_shows_knockout_history_and_pending_actions(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $team = Team::factory()->create();
        $user = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);
        $opponent = User::factory()->create();

        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Singles KO',
            'type' => KnockoutType::Singles,
        ]);

        $round = KnockoutRound::query()->create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $homeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $user->id,
        ]);

        $awayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $knockout->id,
            'player_one_id' => $opponent->id,
        ]);

        $completedMatch = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'home_score' => 4,
            'away_score' => 2,
            'best_of' => 7,
            'starts_at' => now()->subDays(2),
        ]);

        $pendingMatch = KnockoutMatch::query()->create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 2,
            'home_participant_id' => $homeParticipant->id,
            'away_participant_id' => $awayParticipant->id,
            'best_of' => 7,
            'starts_at' => now()->subDay(),
        ]);

        $teamKnockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Team KO',
            'type' => KnockoutType::Team,
        ]);

        $teamRound = KnockoutRound::query()->create([
            'knockout_id' => $teamKnockout->id,
            'name' => 'Semi-finals',
            'position' => 1,
            'scheduled_for' => now()->subDay(),
            'is_visible' => true,
        ]);

        $teamHomeParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => $team->id,
        ]);

        $teamAwayParticipant = KnockoutParticipant::query()->create([
            'knockout_id' => $teamKnockout->id,
            'team_id' => Team::factory()->create()->id,
        ]);

        KnockoutMatch::query()->create([
            'knockout_id' => $teamKnockout->id,
            'knockout_round_id' => $teamRound->id,
            'position' => 1,
            'home_participant_id' => $teamHomeParticipant->id,
            'away_participant_id' => $teamAwayParticipant->id,
            'best_of' => 11,
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-account-knockout-section', false)
            ->assertSeeText('Knockouts')
            ->assertSeeText($knockout->name)
            ->assertSeeText($teamKnockout->name)
            ->assertSeeText('4')
            ->assertSeeText('2')
            ->assertSee(route('knockout.matches.submit', $pendingMatch), false)
            ->assertSee(route('knockout.show', $knockout), false)
            ->assertDontSee(route('knockout.matches.submit', $completedMatch), false)
            ->assertSee(route('knockout.show', $teamKnockout), false);
    }
}
