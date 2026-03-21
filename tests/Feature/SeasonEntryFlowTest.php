<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Livewire\SeasonEntry\Wizard;
use App\Mail\SeasonEntryInvoiceMail;
use App\Models\Knockout;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\SeasonEntry;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SeasonEntryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_signup_page_renders_for_an_open_season(): void
    {
        $season = Season::factory()->create([
            'name' => 'Summer 2026',
            'signup_opens_at' => now()->subDay(),
            'signup_closes_at' => now()->addDays(7),
        ]);

        $this->get(route('season.entry.show', $season))
            ->assertOk()
            ->assertSeeLivewire(Wizard::class)
            ->assertSeeText('Season registration')
            ->assertSeeText('Summer 2026');
    }

    public function test_closed_signup_page_shows_closed_message_and_prevents_submission(): void
    {
        $season = Season::factory()->create([
            'signup_opens_at' => now()->subWeek(),
            'signup_closes_at' => now()->subMinute(),
        ]);

        $this->get(route('season.entry.show', $season))
            ->assertOk()
            ->assertSeeText('currently closed');

        Livewire::test(Wizard::class, ['season' => $season])
            ->set('contact', [
                'name' => 'Chris Heywood',
                'email' => 'chris@example.com',
                'telephone' => '01234 567890',
                'notes' => '',
            ])
            ->call('submit')
            ->assertHasErrors(['season']);
    }

    public function test_public_signup_creates_a_reference_order_and_line_items(): void
    {
        Mail::fake();

        $season = Season::factory()->create([
            'team_entry_fee' => 25,
            'signup_opens_at' => now()->subDay(),
            'signup_closes_at' => now()->addWeek(),
        ]);
        $rulesetOne = Ruleset::factory()->create([
            'name' => 'Division One',
        ]);
        $rulesetTwo = Ruleset::factory()->create([
            'name' => 'Division Two',
        ]);
        $sectionOne = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $rulesetOne->id,
        ]);
        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $rulesetTwo->id,
        ]);
        $singlesKnockout = Knockout::factory()->create([
            'season_id' => $season->id,
            'name' => 'Singles Cup',
            'type' => KnockoutType::Singles,
            'entry_fee' => 6,
        ]);
        $teamKnockout = Knockout::factory()->create([
            'season_id' => $season->id,
            'name' => 'Team Cup',
            'type' => KnockoutType::Team,
            'entry_fee' => 10,
        ]);

        $component = Livewire::test(Wizard::class, ['season' => $season])
            ->set('contact', [
                'name' => 'Chris Heywood',
                'email' => 'chris@example.com',
                'telephone' => '01234 567890',
                'notes' => 'Please confirm once received.',
            ])
            ->set('registrationVenue', [
                'venue_name' => 'Village Club',
                'venue_address' => '1 High Street',
                'venue_telephone' => '09876 543210',
            ])
            ->set('teamRegistrations', [
                [
                    'team_name' => 'Existing Club',
                    'contact_name' => 'Chris Heywood',
                    'contact_telephone' => '01234 567890',
                    'ruleset_id' => $rulesetOne->id,
                    'second_ruleset_id' => $rulesetTwo->id,
                ],
                [
                    'team_name' => 'Newcomers',
                    'contact_name' => 'Carol Wood',
                    'contact_telephone' => '01924 123456',
                    'ruleset_id' => $rulesetTwo->id,
                    'second_ruleset_id' => $rulesetOne->id,
                ],
            ])
            ->set('knockoutRegistrations', [
                [
                    'knockout_id' => $singlesKnockout->id,
                    'entrant_name' => 'Chris Heywood',
                ],
                [
                    'knockout_id' => $teamKnockout->id,
                    'entrant_name' => 'Newcomers',
                ],
            ])
            ->call('submit');

        $entry = SeasonEntry::query()
            ->with(['teams', 'knockoutRegistrations.knockout'])
            ->firstOrFail();

        $component->assertRedirect(route('season.entry.confirmation', [
            'season' => $season,
            'entry' => $entry->reference,
        ]));

        $this->assertNotNull($entry->reference);
        $this->assertSame('66.00', $entry->total_amount);
        $this->assertSame('Village Club', $entry->venue_name);
        $this->assertCount(2, $entry->teams);
        $this->assertCount(2, $entry->knockoutRegistrations);

        $this->assertDatabaseHas('season_team_entries', [
            'season_entry_id' => $entry->id,
            'team_name' => 'Existing Club',
            'contact_name' => 'Chris Heywood',
            'contact_telephone' => '01234 567890',
            'ruleset_id' => $rulesetOne->id,
            'second_ruleset_id' => $rulesetTwo->id,
            'venue_name' => 'Village Club',
            'price' => 25,
        ]);
        $this->assertDatabaseHas('season_team_entries', [
            'season_entry_id' => $entry->id,
            'team_name' => 'Newcomers',
            'contact_name' => 'Carol Wood',
            'contact_telephone' => '01924 123456',
            'ruleset_id' => $rulesetTwo->id,
            'second_ruleset_id' => $rulesetOne->id,
            'venue_name' => 'Village Club',
            'price' => 25,
        ]);
        $this->assertDatabaseHas('season_knockout_entries', [
            'season_entry_id' => $entry->id,
            'knockout_id' => $singlesKnockout->id,
            'entrant_name' => 'Chris Heywood',
            'price' => 6,
        ]);
        $this->assertDatabaseHas('season_knockout_entries', [
            'season_entry_id' => $entry->id,
            'knockout_id' => $teamKnockout->id,
            'entrant_name' => 'Newcomers',
            'price' => 10,
        ]);

        $this->get(route('season.entry.confirmation', [
            'season' => $season,
            'entry' => $entry->reference,
        ]))
            ->assertOk()
            ->assertSee($entry->reference)
            ->assertSeeText('Newcomers')
            ->assertSeeText('Division One')
            ->assertSeeText('Division Two')
            ->assertSee(route('season.entry.invoice', [
                'season' => $season,
                'entry' => $entry->reference,
            ]), false);

        $this->get(route('season.entry.invoice', [
            'season' => $season,
            'entry' => $entry->reference,
        ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        Mail::assertQueued(SeasonEntryInvoiceMail::class, function (SeasonEntryInvoiceMail $mail) use ($entry): bool {
            $attachments = $mail->attachments();

            return $mail->entry->is($entry)
                && count($attachments) === 1
                && $attachments[0] instanceof Attachment;
        });
    }

    public function test_team_registration_page_can_add_and_remove_team_rows(): void
    {
        $season = Season::factory()->create([
            'team_entry_fee' => 25,
            'signup_opens_at' => now()->subDay(),
            'signup_closes_at' => now()->addWeek(),
        ]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'Division One',
        ]);
        $otherRuleset = Ruleset::factory()->create([
            'name' => 'Division Two',
        ]);
        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);
        Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $otherRuleset->id,
        ]);

        Livewire::test(Wizard::class, ['season' => $season])
            ->set('step', 'teams')
            ->set('teamDraft.team_name', 'Newcomers')
            ->set('teamDraft.contact_name', 'Chris Heywood')
            ->set('teamDraft.contact_telephone', '01234 567890')
            ->set('teamDraft.ruleset_id', $ruleset->id)
            ->set('teamDraft.second_ruleset_id', $otherRuleset->id)
            ->call('addTeamRegistration')
            ->assertCount('teamRegistrations', 1)
            ->assertSet('teamRegistrations.0.team_name', 'Newcomers')
            ->assertSet('teamRegistrations.0.contact_name', 'Chris Heywood')
            ->call('removeTeamRegistration', 0)
            ->assertCount('teamRegistrations', 0);
    }
}
