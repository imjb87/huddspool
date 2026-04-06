<?php

namespace Tests\Feature;

use App\Filament\Resources\FixtureResource;
use App\Filament\Resources\FixtureResource\CopilotTools\ListFixturesTool;
use App\Filament\Resources\FixtureResource\CopilotTools\SearchFixturesTool;
use App\Filament\Resources\FixtureResource\CopilotTools\ViewFixtureTool;
use App\Filament\Resources\SeasonEntryResource;
use App\Filament\Resources\SeasonEntryResource\CopilotTools\ListSeasonEntriesTool;
use App\Filament\Resources\SeasonEntryResource\CopilotTools\SearchSeasonEntriesTool;
use App\Filament\Resources\SeasonEntryResource\CopilotTools\ViewSeasonEntryTool;
use App\Filament\Resources\SectionResource;
use App\Filament\Resources\SectionResource\CopilotTools\ListSectionsTool;
use App\Filament\Resources\SectionResource\CopilotTools\SearchSectionsTool;
use App\Filament\Resources\SectionResource\CopilotTools\ViewSectionTool;
use App\Filament\Resources\TeamResource;
use App\Filament\Resources\TeamResource\CopilotTools\ListTeamsTool;
use App\Filament\Resources\TeamResource\CopilotTools\SearchTeamsTool;
use App\Filament\Resources\TeamResource\CopilotTools\ViewTeamTool;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\CopilotTools\ListUsersTool;
use App\Filament\Resources\UserResource\CopilotTools\SearchUsersTool;
use App\Filament\Resources\UserResource\CopilotTools\ViewUserTool;
use App\Filament\Resources\VenueResource;
use App\Filament\Resources\VenueResource\CopilotTools\ListVenuesTool;
use App\Filament\Resources\VenueResource\CopilotTools\SearchVenuesTool;
use App\Filament\Resources\VenueResource\CopilotTools\ViewVenueTool;
use App\Models\Fixture;
use App\Models\SeasonEntry;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Tests\TestCase;

class FilamentCopilotResourceToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_resources_register_list_view_and_search_copilot_tools(): void
    {
        $this->assertEquals([
            ListUsersTool::class,
            ViewUserTool::class,
            SearchUsersTool::class,
        ], array_map(fn (object $tool): string => $tool::class, UserResource::copilotTools()));

        $this->assertEquals([
            ListTeamsTool::class,
            ViewTeamTool::class,
            SearchTeamsTool::class,
        ], array_map(fn (object $tool): string => $tool::class, TeamResource::copilotTools()));

        $this->assertEquals([
            ListFixturesTool::class,
            ViewFixtureTool::class,
            SearchFixturesTool::class,
        ], array_map(fn (object $tool): string => $tool::class, FixtureResource::copilotTools()));

        $this->assertEquals([
            ListVenuesTool::class,
            ViewVenueTool::class,
            SearchVenuesTool::class,
        ], array_map(fn (object $tool): string => $tool::class, VenueResource::copilotTools()));

        $this->assertEquals([
            ListSectionsTool::class,
            ViewSectionTool::class,
            SearchSectionsTool::class,
        ], array_map(fn (object $tool): string => $tool::class, SectionResource::copilotTools()));

        $this->assertEquals([
            ListSeasonEntriesTool::class,
            ViewSeasonEntryTool::class,
            SearchSeasonEntriesTool::class,
        ], array_map(fn (object $tool): string => $tool::class, SeasonEntryResource::copilotTools()));
    }

    public function test_main_resource_copilot_tools_can_list_view_and_search_records(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        $venue = Venue::factory()->create([
            'name' => 'Cue Palace',
        ]);

        $team = Team::factory()->create([
            'name' => 'Black Ballers',
            'venue_id' => $venue->getKey(),
        ]);

        $player = User::factory()->create([
            'name' => 'Alice Break',
            'email' => 'alice@example.test',
            'team_id' => $team->getKey(),
        ]);

        $section = Section::factory()->create([
            'name' => 'Premier Division',
        ]);

        $fixture = Fixture::factory()->create([
            'home_team_id' => $team->getKey(),
            'away_team_id' => Team::factory()->create()->getKey(),
            'section_id' => $section->getKey(),
            'season_id' => $section->season_id,
            'ruleset_id' => $section->ruleset_id,
            'venue_id' => $venue->getKey(),
        ]);

        $entry = SeasonEntry::factory()->create([
            'contact_name' => 'Captain Entry',
            'contact_email' => 'entry@example.test',
            'venue_name' => 'Cue Palace',
        ]);

        $this->assertStringContainsString('Alice Break', (string) (new ListUsersTool)->handle(new Request));
        $this->assertStringContainsString('alice@example.test', (string) (new ViewUserTool)->handle(new Request(['id' => $player->getKey()])));
        $this->assertStringContainsString('Alice Break', (string) (new SearchUsersTool)->handle(new Request(['query' => 'Alice'])));

        $this->assertStringContainsString('Black Ballers', (string) (new ListTeamsTool)->handle(new Request));
        $this->assertStringContainsString('Cue Palace', (string) (new ViewTeamTool)->handle(new Request(['id' => $team->getKey()])));
        $this->assertStringContainsString('Black Ballers', (string) (new SearchTeamsTool)->handle(new Request(['query' => 'Black'])));

        $this->assertStringContainsString('Cue Palace', (string) (new ListVenuesTool)->handle(new Request));
        $this->assertStringContainsString('Cue Palace', (string) (new ViewVenueTool)->handle(new Request(['id' => $venue->getKey()])));
        $this->assertStringContainsString('Cue Palace', (string) (new SearchVenuesTool)->handle(new Request(['query' => 'Cue'])));

        $this->assertStringContainsString('Premier Division', (string) (new ListSectionsTool)->handle(new Request));
        $this->assertStringContainsString('Premier Division', (string) (new ViewSectionTool)->handle(new Request(['id' => $section->getKey()])));
        $this->assertStringContainsString('Premier Division', (string) (new SearchSectionsTool)->handle(new Request(['query' => 'Premier'])));

        $this->assertStringContainsString('Black Ballers', (string) (new ListFixturesTool)->handle(new Request));
        $this->assertStringContainsString('Premier Division', (string) (new ViewFixtureTool)->handle(new Request(['id' => $fixture->getKey()])));
        $this->assertStringContainsString('Black Ballers', (string) (new SearchFixturesTool)->handle(new Request(['query' => 'Black'])));

        $this->assertStringContainsString($entry->reference, (string) (new ListSeasonEntriesTool)->handle(new Request));
        $this->assertStringContainsString('Captain Entry', (string) (new ViewSeasonEntryTool)->handle(new Request(['id' => $entry->getKey()])));
        $this->assertStringContainsString('Captain Entry', (string) (new SearchSeasonEntriesTool)->handle(new Request(['query' => 'Captain'])));
    }
}
