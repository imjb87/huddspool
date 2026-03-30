<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_endpoint_returns_grouped_results_for_trimmed_queries(): void
    {
        Model::withoutEvents(function (): void {
            $season = Season::factory()->create(['is_open' => true]);
            $section = Section::factory()->create(['season_id' => $season->id]);
            $team = Team::factory()->create(['name' => 'Imperial Club']);

            $team->sections()->attach($section);

            User::factory()->create([
                'name' => 'Alex Carter',
                'team_id' => $team->id,
            ]);

            Venue::factory()->create([
                'name' => 'Imperial Hall',
                'address' => '12 West Street',
                'latitude' => 53.6486,
                'longitude' => -1.7828,
            ]);
        });

        $response = $this->getJson(route('search.index', ['q' => '  Imperial  ']));

        $response->assertOk();
        $response->assertJsonCount(3, 'groups');
        $response->assertJsonPath('groups.0.heading', 'Players');
        $response->assertJsonPath('groups.0.results.0.name', 'Alex Carter');
        $response->assertJsonPath('groups.0.results.0.secondaryText', 'Imperial Club');
        $response->assertJsonPath('groups.1.heading', 'Teams');
        $response->assertJsonPath('groups.1.results.0.name', 'Imperial Club');
        $response->assertJsonPath('groups.2.heading', 'Venues');
        $response->assertJsonPath('groups.2.results.0.name', 'Imperial Hall');
        $response->assertJsonPath('groups.2.results.0.secondaryText', '12 West Street');
    }

    public function test_search_endpoint_returns_empty_groups_for_short_queries(): void
    {
        $response = $this->getJson(route('search.index', ['q' => 'ab']));

        $response->assertOk();
        $response->assertExactJson([
            'groups' => [],
        ]);
    }
}
