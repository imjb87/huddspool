<?php

namespace Tests\Feature;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenuePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_venue_page_uses_the_redesigned_public_layout(): void
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'Division One',
        ]);

        $venue = Venue::factory()->create([
            'name' => 'Riverside Club',
            'address' => '1 High Street, Huddersfield',
            'telephone' => '01234 567890',
        ]);

        $captain = User::factory()->create(['name' => 'Chris Heywood']);
        $team = Team::factory()->create([
            'name' => 'Golcar Legion Z',
            'venue_id' => $venue->id,
            'captain_id' => $captain->id,
        ]);

        $section->teams()->attach($team->id, ['sort' => 1]);

        $response = $this->get(route('venue.show', $venue));

        $response->assertOk();
        $response->assertSee('data-venue-page', false);
        $response->assertSee('ui-page-shell', false);
        $response->assertSee('data-section-shared-header', false);
        $response->assertSee('data-venue-info-section', false);
        $response->assertSee('data-venue-teams-section', false);
        $response->assertSee('data-venue-teams-list', false);
        $response->assertSee('data-venue-map-section', false);
        $response->assertSee('ui-shell-grid', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-card-rows', false);
        $response->assertSee('ui-card-row-link', false);
        $response->assertSee('dark:bg-neutral-950', false);
        $response->assertSee('dark:border-neutral-800/80', false);
        $response->assertSee('dark:text-gray-100', false);
        $response->assertSeeText($venue->name);
        $response->assertSeeText('Venue');
        $response->assertSeeText('Venue information');
        $response->assertSeeText('Teams');
        $response->assertSeeText('Map');
        $response->assertSeeText($venue->address);
        $response->assertSeeText($venue->telephone);
        $response->assertSeeText($team->name);
        $response->assertSeeText($section->name);
        $response->assertSeeText($captain->name);
        $response->assertSee(route('team.show', $team), false);
    }
}
