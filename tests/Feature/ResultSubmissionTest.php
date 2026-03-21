<?php

namespace Tests\Feature;

use App\Livewire\ResultForm;
use App\Mail\LeagueResultSubmittedMail;
use App\Models\Fixture;
use App\Models\FixtureResultLock;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class ResultSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_result_create_route_displays_the_redesigned_submission_layout(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
        ] = $this->createResultFormLockContext();

        $this->actingAs($teamAdmin)
            ->get(route('result.create', $fixture))
            ->assertOk()
            ->assertSee('data-result-create-page', false)
            ->assertSee('data-result-create-info-section', false)
            ->assertSee('data-result-create-form-section', false)
            ->assertSee('data-result-form', false)
            ->assertSee('data-result-form-shell', false)
            ->assertSee('data-result-form-frames', false)
            ->assertSee('dark:bg-zinc-900', false)
            ->assertSee('dark:border-zinc-800/80', false)
            ->assertSeeText('Submit a result')
            ->assertSeeText('Fixture details')
            ->assertSeeText('Result card');
    }

    public function test_team_admin_can_save_partial_frames(): void
    {
        Carbon::setTestNow('2026-03-13 19:00:00');
        Mail::fake();

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

        $component->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id);
        $component->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id);
        $component->set('form.frames.1.home_score', 1);

        $result = Result::first();

        $this->assertNotNull($result);
        $this->assertFalse($result->is_confirmed);
        $this->assertSame(1, $result->home_score);
        $this->assertSame(0, $result->away_score);
        $this->assertSame(0, $result->submitted_by);
        $this->assertNull($result->submitted_at);
        $this->assertCount(1, $result->frames);
        $this->assertEquals((int) $homePlayers[0]->id, $result->frames->first()->home_player_id);
        Mail::assertNothingQueued();

        $component->assertSet('form.homeScore', 1);
        $component->assertSet('form.awayScore', 0);

        Carbon::setTestNow();
    }

    public function test_locking_result_requires_all_frames_and_confirms_result(): void
    {
        Carbon::setTestNow('2026-03-13 19:00:00');
        Mail::fake();

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
            'email' => 'submitter@example.com',
        ]);
        $homeTeam->update(['captain_id' => User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
            'email' => 'home-captain@example.com',
        ])->id]);
        $homeSecondaryAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
            'email' => 'home-admin@example.com',
        ]);
        $awayCaptain = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => 1,
            'is_admin' => false,
            'email' => 'away-captain@example.com',
        ]);
        $awayTeam->update(['captain_id' => $awayCaptain->id]);
        $awayAdmin = User::factory()->create([
            'team_id' => $awayTeam->id,
            'role' => 2,
            'is_admin' => false,
            'email' => 'away-admin@example.com',
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

        $component->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id);
        $component->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id);
        $component->set('form.frames.1.home_score', 1);

        $draftCreatedAt = Result::firstOrFail()->created_at;

        Carbon::setTestNow('2026-03-13 21:15:00');

        for ($i = 2; $i <= 10; $i++) {
            $homePlayer = $homePlayers[intdiv($i - 1, 2)];
            $awayPlayer = $awayPlayers[intdiv($i - 1, 2)];

            $component->set("form.frames.$i.home_player_id", (string) $homePlayer->id);
            $component->set("form.frames.$i.away_player_id", (string) $awayPlayer->id);

            if ($i % 2 === 1) {
                $component->set("form.frames.$i.home_score", 1);
                $component->set("form.frames.$i.away_score", 0);
            } else {
                $component->set("form.frames.$i.home_score", 0);
                $component->set("form.frames.$i.away_score", 1);
            }
        }

        $component->call('submit');

        $result = Result::first();

        $this->assertNotNull($result);
        $this->assertTrue($result->is_confirmed);
        $this->assertSame(5, $result->home_score);
        $this->assertSame(5, $result->away_score);
        $this->assertSame($teamAdmin->id, $result->submitted_by);
        $this->assertNotNull($result->submitted_at);
        $this->assertTrue($result->submitted_at->equalTo(Carbon::now()));
        $this->assertTrue($result->created_at->equalTo($draftCreatedAt));
        $this->assertFalse($result->submitted_at->equalTo($result->created_at));
        $this->assertSame(10, $result->frames()->count());
        Mail::assertQueued(LeagueResultSubmittedMail::class, function (LeagueResultSubmittedMail $mail) use ($teamAdmin, $homeSecondaryAdmin, $awayCaptain, $awayAdmin, $result) {
            return $mail->hasTo($teamAdmin->email)
                && $mail->hasCc($homeSecondaryAdmin->email)
                && $mail->hasCc($awayCaptain->email)
                && $mail->hasCc($awayAdmin->email)
                && ! $mail->hasCc($teamAdmin->email)
                && $mail->result->is($result);
        });

        $component->assertRedirect(route('result.show', $result));

        $response = $this->get(route('result.show', $result));
        $response->assertOk();
        $response->assertSeeText('21:15');
        $response->assertDontSeeText('19:00');

        Carbon::setTestNow();
    }

    public function test_league_result_submitted_mail_uses_the_markdown_mail_template(): void
    {
        $result = Result::factory()->make([
            'id' => 123,
            'home_team_name' => 'Home',
            'home_score' => 5,
            'away_score' => 4,
            'away_team_name' => 'Away',
            'submitted_at' => Carbon::parse('2026-03-13 21:15:00'),
        ]);

        $result->setRelation('submittedBy', User::factory()->make(['name' => 'John Smith']));
        $result->setRelation('fixture', Fixture::factory()->make([
            'fixture_date' => Carbon::parse('2026-03-12'),
        ]));
        $result->fixture->setRelation('section', Section::factory()->make(['name' => 'Premier']));
        $result->fixture->section->setRelation('ruleset', Ruleset::factory()->make(['name' => 'International']));

        $html = app(Markdown::class)->render(
            'mail.league-result-submitted',
            ['result' => $result],
        )->toHtml();

        $this->assertStringContainsString('League result submitted', $html);
        $this->assertStringContainsString('View submitted result', $html);
        $this->assertStringContainsString('league result submitted', strtolower($html));
        $this->assertStringContainsString('images/logo.png', $html);
        $this->assertStringNotContainsString('notification-logo.png', $html);
    }

    public function test_partial_autosave_does_not_delete_existing_frames_when_a_saved_frame_becomes_incomplete(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createResultFormLockContext();

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        $component->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id);
        $component->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id);
        $component->set('form.frames.1.home_score', 1);

        $result = Result::firstOrFail()->load(['frames' => fn ($query) => $query->orderBy('id')]);
        $firstFrameId = $result->frames[0]->id;

        $component->set('form.frames.2.home_player_id', (string) $homePlayers[1]->id);
        $component->set('form.frames.2.away_player_id', (string) $awayPlayers[1]->id);
        $component->set('form.frames.2.home_score', 1);

        $result = $result->fresh(['frames' => fn ($query) => $query->orderBy('id')]);

        $this->assertCount(2, $result->frames);
        $this->assertSame($firstFrameId, $result->frames[0]->id);

        $component->set('form.frames.2.home_score', 0);

        $result = $result->fresh(['frames' => fn ($query) => $query->orderBy('id')]);

        $this->assertCount(2, $result->frames);
        $this->assertSame($firstFrameId, $result->frames[0]->id);
        $this->assertSame(2, $result->home_score);
        $this->assertSame(0, $result->away_score);
    }

    public function test_partial_autosave_persists_the_completed_follow_up_after_an_incomplete_edit(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createResultFormLockContext();

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        $component->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id);
        $component->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id);
        $component->set('form.frames.1.home_score', 1);

        $component->set('form.frames.2.home_player_id', (string) $homePlayers[1]->id);
        $component->set('form.frames.2.away_player_id', (string) $awayPlayers[1]->id);
        $component->set('form.frames.2.home_score', 1);

        $component->set('form.frames.2.home_score', 0);
        $component->set('form.frames.2.away_score', 1);

        $result = Result::firstOrFail()->fresh(['frames' => fn ($query) => $query->orderBy('id')]);

        $this->assertCount(2, $result->frames);
        $this->assertSame(1, $result->home_score);
        $this->assertSame(1, $result->away_score);
        $this->assertSame(0, $result->frames[1]->home_score);
        $this->assertSame(1, $result->frames[1]->away_score);
    }

    public function test_player_selections_persist_after_scoring_one_frame_with_all_players_selected(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createResultFormLockContext();

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        for ($i = 1; $i <= 10; $i++) {
            $homePlayer = $homePlayers[($i - 1) % $homePlayers->count()];
            $awayPlayer = $awayPlayers[($i - 1) % $awayPlayers->count()];

            $component->set("form.frames.$i.home_player_id", (string) $homePlayer->id);
            $component->set("form.frames.$i.away_player_id", (string) $awayPlayer->id);
        }

        $component->set('form.frames.1.home_score', 1);

        for ($i = 1; $i <= 10; $i++) {
            $homePlayer = $homePlayers[($i - 1) % $homePlayers->count()];
            $awayPlayer = $awayPlayers[($i - 1) % $awayPlayers->count()];

            $component->assertSet("form.frames.$i.home_player_id", (string) $homePlayer->id);
            $component->assertSet("form.frames.$i.away_player_id", (string) $awayPlayer->id);
        }
    }

    public function test_selected_players_render_avatar_previews_in_the_result_form(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createResultFormLockContext();

        Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id)
            ->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id)
            ->assertSee($homePlayers[0]->avatar_url, false)
            ->assertSee($awayPlayers[0]->avatar_url, false);
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
            ->set('form.frames.1.home_player_id', (string) $homePlayer->id)
            ->set('form.frames.1.away_player_id', (string) $awayPlayer->id)
            ->set('form.frames.1.home_score', 1);

        $result = Result::first();

        $response = $this->get(route('result.show', $result));
        $response->assertOk();
        $response->assertDontSeeText('Continue submitting result');

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

    public function test_keep_lock_alive_detects_when_another_team_admin_takes_over_after_lock_expiry(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $primaryAdmin,
            'secondaryAdmin' => $secondaryAdmin,
        ] = $this->createResultFormLockContext();

        $primaryComponent = Livewire::actingAs($primaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSet('canEdit', true)
            ->assertSet('lockedByAnother', false);

        FixtureResultLock::query()->update(['locked_until' => now()->subMinute()]);

        Livewire::actingAs($secondaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSet('canEdit', true)
            ->assertSet('lockedByAnother', false);

        $this->actingAs($primaryAdmin);

        $primaryComponent
            ->call('keepLockAlive')
            ->assertSet('canEdit', false)
            ->assertSet('lockedByAnother', true)
            ->assertSet('lockOwnerName', $secondaryAdmin->name);

        $this->assertDatabaseHas('fixture_result_locks', [
            'fixture_id' => $fixture->id,
            'locked_by' => $secondaryAdmin->id,
        ]);
    }

    public function test_submit_rechecks_edit_lock_before_confirming_result(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $primaryAdmin,
            'secondaryAdmin' => $secondaryAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createResultFormLockContext();

        $primaryComponent = Livewire::actingAs($primaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        $this->fillCompletedFrames($primaryComponent, $homePlayers, $awayPlayers);

        $draftResult = Result::firstOrFail();

        FixtureResultLock::query()->update(['locked_until' => now()->subMinute()]);

        Livewire::actingAs($secondaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSet('canEdit', true)
            ->assertSet('lockedByAnother', false);

        $this->actingAs($primaryAdmin);

        $primaryComponent
            ->call('submit')
            ->assertRedirect(route('result.show', $draftResult));

        $draftResult->refresh();

        $this->assertFalse($draftResult->is_confirmed);
        $this->assertSame(0, $draftResult->submitted_by);
        $this->assertNull($draftResult->submitted_at);
    }

    public function test_submit_detects_when_result_has_been_confirmed_by_another_admin(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $primaryAdmin,
            'secondaryAdmin' => $secondaryAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createResultFormLockContext();

        $primaryComponent = Livewire::actingAs($primaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        $this->fillCompletedFrames($primaryComponent, $homePlayers, $awayPlayers);

        $draftResult = Result::firstOrFail();
        $draftResult->update([
            'is_confirmed' => true,
            'submitted_by' => $secondaryAdmin->id,
            'submitted_at' => now(),
        ]);

        $primaryComponent
            ->call('submit')
            ->assertRedirect(route('result.show', $draftResult));

        $draftResult->refresh();

        $this->assertTrue($draftResult->is_confirmed);
        $this->assertSame($secondaryAdmin->id, $draftResult->submitted_by);
        $this->assertNotNull($draftResult->submitted_at);
    }

    public function test_derived_result_form_state_properties_are_locked_from_client_updates(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $primaryAdmin,
        ] = $this->createResultFormLockContext();

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($primaryAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->set('form.homeScore', 999);
    }

    /**
     * @return array{
     *     fixture: Fixture,
     *     primaryAdmin: User,
     *     secondaryAdmin: User,
     *     homePlayers: Collection<int, User>,
     *     awayPlayers: Collection<int, User>
     * }
     */
    private function createResultFormLockContext(): array
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

        return compact('fixture', 'primaryAdmin', 'secondaryAdmin', 'homePlayers', 'awayPlayers');
    }

    /**
     * @param  Collection<int, User>  $homePlayers
     * @param  Collection<int, User>  $awayPlayers
     */
    private function fillCompletedFrames($component, $homePlayers, $awayPlayers): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $homePlayer = $homePlayers[intdiv($i - 1, 2)];
            $awayPlayer = $awayPlayers[intdiv($i - 1, 2)];

            $component->set("form.frames.$i.home_player_id", (string) $homePlayer->id);
            $component->set("form.frames.$i.away_player_id", (string) $awayPlayer->id);

            if ($i % 2 === 1) {
                $component->set("form.frames.$i.home_score", 1);
                $component->set("form.frames.$i.away_score", 0);
            } else {
                $component->set("form.frames.$i.home_score", 0);
                $component->set("form.frames.$i.away_score", 1);
            }
        }
    }
}
