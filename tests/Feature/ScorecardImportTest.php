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
use App\Support\Scorecard\ScorecardExtractionResult;
use App\Support\Scorecard\ScorecardInterpretationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ScorecardImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_scorecard_import_section_is_shown_to_authorized_team_admin(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
        ] = $this->createScorecardContext();

        Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertSee('data-result-scorecard-import-section', false)
            ->assertSee('data-scorecard-photo-input', false)
            ->assertSee('data-scorecard-import-button', false)
            ->assertSeeText('Scan scorecard')
            ->assertSeeText('Import scorecard');
    }

    public function test_scorecard_import_section_is_hidden_when_result_is_locked(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
            'section' => $section,
            'ruleset' => $ruleset,
        ] = $this->createScorecardContext();

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_score' => 6,
            'away_score' => 4,
            'is_confirmed' => true,
        ]);

        Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->assertDontSee('data-result-scorecard-import-section', false)
            ->assertDontSee('data-scorecard-import-button', false);
    }

    public function test_unauthorized_player_cannot_access_result_create_route(): void
    {
        [
            'fixture' => $fixture,
            'homeTeam' => $homeTeam,
        ] = $this->createScorecardContext();

        $player = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 1,
            'is_admin' => false,
        ]);

        $this->actingAs($player)
            ->get(route('result.create', $fixture))
            ->assertForbidden();
    }

    public function test_successful_extraction_prefills_form_state(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createScorecardContext();

        $this->mock(ScorecardInterpretationService::class)
            ->shouldReceive('interpret')
            ->once()
            ->andReturn(new ScorecardExtractionResult(
                frames: [
                    1 => [
                        'home_player_name' => $homePlayers[0]->name,
                        'away_player_name' => $awayPlayers[0]->name,
                        'home_score' => 1,
                        'away_score' => 0,
                    ],
                    2 => [
                        'home_player_name' => $homePlayers[1]->name,
                        'away_player_name' => $awayPlayers[1]->name,
                        'home_score' => 0,
                        'away_score' => 1,
                    ],
                ],
                warnings: [],
            ));

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->set('scorecardPhoto', UploadedFile::fake()->image('scorecard.jpg'))
            ->call('importScorecard');

        $component
            ->assertSet('scorecardImportStatus', 'success')
            ->assertSet('scorecardImportCount', 2)
            ->assertSet('scorecardWarnings', [])
            ->assertSet('form.frames.1.home_player_id', (string) $homePlayers[0]->id)
            ->assertSet('form.frames.1.away_player_id', (string) $awayPlayers[0]->id)
            ->assertSet('form.frames.1.home_score', 1)
            ->assertSet('form.frames.1.away_score', 0)
            ->assertSet('form.frames.2.home_player_id', (string) $homePlayers[1]->id)
            ->assertSet('form.frames.2.away_player_id', (string) $awayPlayers[1]->id)
            ->assertSet('form.frames.2.home_score', 0)
            ->assertSet('form.frames.2.away_score', 1);

        $result = Result::firstOrFail();
        $this->assertFalse($result->is_confirmed);
        $this->assertSame((int) $homePlayers[0]->id, (int) data_get($result->draft_state, '1.home_player_id'));
        $this->assertSame((int) $awayPlayers[0]->id, (int) data_get($result->draft_state, '1.away_player_id'));
    }

    public function test_unresolved_player_names_produce_warnings_and_leave_player_ids_as_null(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
        ] = $this->createScorecardContext();

        $this->mock(ScorecardInterpretationService::class)
            ->shouldReceive('interpret')
            ->once()
            ->andReturn(new ScorecardExtractionResult(
                frames: [
                    1 => [
                        'home_player_name' => 'Completely Unknown Person ZZZZZ',
                        'away_player_name' => 'Another Unresolvable Name QQQQQ',
                        'home_score' => 1,
                        'away_score' => 0,
                    ],
                ],
                warnings: [],
            ));

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->set('scorecardPhoto', UploadedFile::fake()->image('scorecard.jpg'))
            ->call('importScorecard');

        $component
            ->assertSet('scorecardImportStatus', 'success')
            ->assertSet('scorecardImportCount', 1)
            ->assertSet('form.frames.1.home_player_id', null)
            ->assertSet('form.frames.1.away_player_id', null)
            ->assertSet('form.frames.1.home_score', 1)
            ->assertSet('form.frames.1.away_score', 0);

        $warnings = $component->get('scorecardWarnings');
        $this->assertNotEmpty($warnings);
        $this->assertStringContainsString('could not be matched', implode(' ', $warnings));
    }

    public function test_extraction_failure_returning_empty_frames_leaves_existing_draft_intact(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createScorecardContext();

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        // Establish an existing draft with frame 1 filled in.
        $component
            ->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id)
            ->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id)
            ->set('form.frames.1.home_score', 1);

        $draftVersion = $component->get('draftVersion');

        $this->mock(ScorecardInterpretationService::class)
            ->shouldReceive('interpret')
            ->once()
            ->andReturn(new ScorecardExtractionResult(
                frames: [],
                warnings: ['Could not read any frames from the image.'],
            ));

        $component
            ->set('scorecardPhoto', UploadedFile::fake()->image('scorecard.jpg'))
            ->call('importScorecard');

        // Existing draft state must be untouched.
        $component
            ->assertSet('scorecardImportStatus', 'error')
            ->assertSet('form.frames.1.home_player_id', (string) $homePlayers[0]->id)
            ->assertSet('form.frames.1.away_player_id', (string) $awayPlayers[0]->id)
            ->assertSet('form.frames.1.home_score', 1)
            ->assertSet('draftVersion', $draftVersion);
    }

    public function test_extraction_exception_leaves_existing_draft_intact(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createScorecardContext();

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture]);

        $component
            ->set('form.frames.1.home_player_id', (string) $homePlayers[0]->id)
            ->set('form.frames.1.away_player_id', (string) $awayPlayers[0]->id)
            ->set('form.frames.1.home_score', 1);

        $draftVersion = $component->get('draftVersion');

        $this->mock(ScorecardInterpretationService::class)
            ->shouldReceive('interpret')
            ->once()
            ->andThrow(new \RuntimeException('API error'));

        $component
            ->set('scorecardPhoto', UploadedFile::fake()->image('scorecard.jpg'))
            ->call('importScorecard');

        $component
            ->assertSet('scorecardImportStatus', 'error')
            ->assertSet('form.frames.1.home_player_id', (string) $homePlayers[0]->id)
            ->assertSet('form.frames.1.away_player_id', (string) $awayPlayers[0]->id)
            ->assertSet('form.frames.1.home_score', 1)
            ->assertSet('draftVersion', $draftVersion);
    }

    public function test_final_submission_still_validates_after_partial_scorecard_import(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homePlayers' => $homePlayers,
            'awayPlayers' => $awayPlayers,
        ] = $this->createScorecardContext();

        // Import fills only frame 1 (incomplete — 9 frames missing).
        $this->mock(ScorecardInterpretationService::class)
            ->shouldReceive('interpret')
            ->once()
            ->andReturn(new ScorecardExtractionResult(
                frames: [
                    1 => [
                        'home_player_name' => $homePlayers[0]->name,
                        'away_player_name' => $awayPlayers[0]->name,
                        'home_score' => 1,
                        'away_score' => 0,
                    ],
                ],
                warnings: [],
            ));

        $component = Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->set('scorecardPhoto', UploadedFile::fake()->image('scorecard.jpg'))
            ->call('importScorecard');

        $component->assertSet('scorecardImportStatus', 'success');

        // Submit without completing all 10 frames — must fail validation.
        $component->call('submit')
            ->assertHasErrors(['form.frames']);
    }

    public function test_scorecard_import_does_nothing_when_result_is_locked(): void
    {
        [
            'fixture' => $fixture,
            'primaryAdmin' => $teamAdmin,
            'homeTeam' => $homeTeam,
            'awayTeam' => $awayTeam,
            'section' => $section,
            'ruleset' => $ruleset,
        ] = $this->createScorecardContext();

        Result::factory()->create([
            'fixture_id' => $fixture->id,
            'home_team_id' => $homeTeam->id,
            'home_team_name' => $homeTeam->name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->name,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_score' => 6,
            'away_score' => 4,
            'is_confirmed' => true,
        ]);

        $this->mock(ScorecardInterpretationService::class)
            ->shouldNotReceive('interpret');

        Livewire::actingAs($teamAdmin)
            ->test(ResultForm::class, ['fixture' => $fixture])
            ->set('scorecardPhoto', UploadedFile::fake()->image('scorecard.jpg'))
            ->call('importScorecard')
            ->assertSet('scorecardImportStatus', null);
    }

    /**
     * @return array{
     *     fixture: Fixture,
     *     primaryAdmin: User,
     *     homeTeam: Team,
     *     awayTeam: Team,
     *     section: Section,
     *     ruleset: Ruleset,
     *     homePlayers: Collection<int, User>,
     *     awayPlayers: Collection<int, User>
     * }
     */
    private function createScorecardContext(): array
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

        return compact('fixture', 'primaryAdmin', 'homeTeam', 'awayTeam', 'section', 'ruleset', 'homePlayers', 'awayPlayers');
    }
}
