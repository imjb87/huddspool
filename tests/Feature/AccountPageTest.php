<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\KnockoutType;
use App\Livewire\Account\Show as AccountShow;
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

    public function test_authenticated_user_can_view_account_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('data-account-page', false)
            ->assertSee('data-account-header', false)
            ->assertSee('data-account-profile-section', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertSeeText('Email address');
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
        $uploadedPath = $user->avatar_path;

        $this->assertNotNull($uploadedPath);
        $this->assertStringStartsWith('avatars/', $uploadedPath);
        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
        Storage::disk('public')->assertExists($uploadedPath);

        Livewire::actingAs($user)
            ->test(AccountShow::class)
            ->call('clearAvatar')
            ->call('saveProfile');

        $this->assertNull($user->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($uploadedPath);
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

    public function test_team_admin_sees_result_submission_prompt_on_account_page_when_fixture_is_due(): void
    {
        $season = Season::factory()->create(['is_open' => false]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        $team = Team::factory()->create();
        $opponentTeam = Team::factory()->create();
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
            ->assertSee('data-account-result-submission-prompt', false)
            ->assertSeeText('A team result is ready to submit.')
            ->assertSee(route('result.create', $fixture), false);
    }

    public function test_captain_sees_team_nav_link_on_account_page(): void
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
            ->assertSee('href="'.route('account.team').'"', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertSeeText('Team')
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
            'home_score' => 4,
            'away_score' => 2,
            'winner_participant_id' => $teamHomeParticipant->id,
            'best_of' => 7,
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
            ->assertSee('data-account-team-info-section', false)
            ->assertSee('data-account-team-section', false)
            ->assertSee('data-account-team-member-stats', false)
            ->assertSee('data-account-team-fixtures-section', false)
            ->assertSee('data-account-team-knockout-section', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertSeeText('Team members')
            ->assertSeeText('Fixtures')
            ->assertSeeText('Team knockouts')
            ->assertSeeText($team->name)
            ->assertSeeText('Premier Division')
            ->assertSeeText('Team Admin')
            ->assertSeeText($team->name)
            ->assertSeeText($opponentTeam->name)
            ->assertSeeText($teamKnockout->name)
            ->assertSee('href="'.route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]).'"', false)
            ->assertSee('href="'.route('venue.show', $team->venue).'"', false)
            ->assertSee('href="'.route('player.show', $teamAdmin).'"', false)
            ->assertSee(route('result.create', $dueFixture), false)
            ->assertSee(route('result.create', $continueFixture), false)
            ->assertDontSee(route('result.create', $futureFixture), false)
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

    public function test_account_page_shows_player_history_sections(): void
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
            ->assertDontSeeText($teamKnockout->name)
            ->assertSeeText('4')
            ->assertSeeText('2')
            ->assertSee(route('knockout.matches.submit', $pendingMatch), false)
            ->assertSee(route('knockout.show', $knockout), false)
            ->assertDontSee(route('knockout.matches.submit', $completedMatch), false);
    }

    public function test_captain_can_promote_and_remove_team_members_from_account_page(): void
    {
        $team = Team::factory()->create();

        $captain = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::Player->value,
        ]);

        $team->update(['captain_id' => $captain->id]);

        $promotedMember = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::Player->value,
        ]);

        $removedMember = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        Livewire::actingAs($captain)
            ->test(AccountShow::class)
            ->call('promoteToTeamAdmin', $promotedMember->id);

        $this->assertSame(UserRole::TeamAdmin->value, $promotedMember->fresh()->role);

        Livewire::actingAs($captain)
            ->test(AccountShow::class)
            ->call('removeFromTeam', $removedMember->id);

        $this->assertNull($removedMember->fresh()->team_id);
        $this->assertSame(UserRole::Player->value, $removedMember->fresh()->role);
    }

    public function test_non_captain_can_not_manage_team_members_from_account_page(): void
    {
        $team = Team::factory()->create();

        $teamAdmin = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $member = User::factory()->create([
            'team_id' => $team->id,
            'role' => UserRole::Player->value,
        ]);

        Livewire::actingAs($teamAdmin)
            ->test(AccountShow::class)
            ->call('promoteToTeamAdmin', $member->id)
            ->assertForbidden();

        Livewire::actingAs($teamAdmin)
            ->test(AccountShow::class)
            ->call('removeFromTeam', $member->id)
            ->assertForbidden();
    }

}
