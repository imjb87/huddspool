<?php

namespace Tests\Feature;

use App\Filament\Resources\ExpulsionResource;
use App\Filament\Resources\KnockoutResource;
use App\Filament\Resources\SeasonEntryResource;
use App\Filament\Resources\SeasonEntryResource\Pages\EditSeasonEntry;
use App\Filament\Resources\SeasonEntryResource\RelationManagers\KnockoutRegistrationsRelationManager;
use App\Filament\Resources\SeasonEntryResource\RelationManagers\TeamsRelationManager;
use App\Filament\Resources\SeasonResource\Pages\EditSeason;
use App\Filament\Resources\SectionResource;
use App\Models\Knockout;
use App\Models\Season;
use App\Models\SeasonEntry;
use App\Models\SeasonKnockoutEntry;
use App\Models\SeasonTeamEntry;
use App\Models\Section;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeasonEntryAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_season_edit_page_uses_sub_navigation_for_nested_management_pages(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $season = Season::factory()->create();

        Filament::setCurrentPanel('admin');

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.seasons.edit', ['record' => $season]))
            ->assertOk()
            ->assertSeeText('Season')
            ->assertSeeText('Sections')
            ->assertSeeText('Knockouts')
            ->assertSeeText('Entries')
            ->assertSeeText('Expulsions')
            ->assertDontSeeText('Import section')
            ->assertSee(SectionResource::getUrl('index', ['season' => $season]), false)
            ->assertSee(KnockoutResource::getUrl('index', ['season' => $season]), false)
            ->assertSee(SeasonEntryResource::getUrl('index', ['season' => $season]), false)
            ->assertSee(ExpulsionResource::getUrl('index', ['season' => $season]), false);
    }

    public function test_sections_sub_page_shows_section_names_and_import_action(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $season = Season::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'name' => 'Premier Division',
        ]);

        Filament::setCurrentPanel('admin');

        $this->actingAs($admin)
            ->get(SectionResource::getUrl('index', ['season' => $season]))
            ->assertOk()
            ->assertSeeText('Import section')
            ->assertSeeText($section->name)
            ->assertSee(SectionResource::getUrl('edit', ['record' => $section, 'season' => $season]), false);
    }

    public function test_admin_can_mark_an_entry_paid_from_filament(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $entry = SeasonEntry::factory()->create();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditSeasonEntry::class, [
                'record' => $entry->getRouteKey(),
                'parentRecord' => $entry->season,
            ])
            ->callAction('markPaid');

        $this->assertNotNull($entry->fresh()->paid_at);
    }

    public function test_admin_can_view_entry_teams_and_knockout_entries_in_filament(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $season = Season::factory()->create();
        $entry = SeasonEntry::factory()->create([
            'season_id' => $season->id,
        ]);
        $teamRegistration = SeasonTeamEntry::factory()->create([
            'season_entry_id' => $entry->id,
            'team_name' => 'Lakeside A',
            'venue_name' => 'Lakeside Club',
        ]);
        $knockoutRegistration = SeasonKnockoutEntry::factory()->create([
            'season_entry_id' => $entry->id,
            'knockout_id' => Knockout::factory()->create([
                'season_id' => $season->id,
                'name' => 'Open Singles',
            ])->id,
            'entrant_name' => 'Chris Heywood',
        ]);

        Filament::setCurrentPanel('admin');

        $this->actingAs($admin)
            ->get(SeasonEntryResource::getUrl('edit', [
                'record' => $entry,
                'season' => $season,
            ]))
            ->assertOk()
            ->assertSee($entry->reference);

        Livewire::actingAs($admin)
            ->test(TeamsRelationManager::class, [
                'ownerRecord' => $entry,
                'pageClass' => EditSeasonEntry::class,
            ])
            ->assertCanSeeTableRecords([$teamRegistration]);

        Livewire::actingAs($admin)
            ->test(KnockoutRegistrationsRelationManager::class, [
                'ownerRecord' => $entry,
                'pageClass' => EditSeasonEntry::class,
            ])
            ->assertCanSeeTableRecords([$knockoutRegistration]);
    }

    public function test_season_edit_page_can_generate_18_weekly_dates_from_the_first_week_date(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $season = Season::factory()->create([
            'dates' => [],
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditSeason::class, [
                'record' => $season->getRouteKey(),
            ])
            ->set('data.first_week_date', '2026-08-04')
            ->assertSet('data.dates', [])
            ->callFormComponentAction('first_week_date', 'generateWeeks')
            ->assertSet('data.dates.0.date', '2026-08-04')
            ->assertSet('data.dates.1.date', '2026-08-11')
            ->assertSet('data.dates.17.date', '2026-12-01');
    }
}
