<?php

namespace Tests\Feature;

use App\Livewire\Search;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SearchComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_handles_array_search_term_without_throwing_view_exception(): void
    {
        Livewire::test(Search::class)
            ->set('searchTerm', ['unexpected'])
            ->assertSeeText('Search for players, teams and venues')
            ->assertSeeText('Search for players, teams and venues');
    }

    public function test_component_searches_with_a_trimmed_live_query(): void
    {
        Venue::withoutEvents(function (): void {
            Venue::factory()->create([
                'name' => 'Imperial Club',
                'address' => '12 West Street',
                'latitude' => 53.6486,
                'longitude' => -1.7828,
            ]);
        });

        Livewire::test(Search::class)
            ->set('searchTerm', '  Imperial Club  ')
            ->assertSeeText('Imperial Club')
            ->assertSeeText('12 West Street')
            ->assertSeeText('Venue')
            ->assertSee('data-search-result-link', false);
    }

    public function test_component_only_matches_players_by_their_own_name(): void
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
        });

        Livewire::test(Search::class)
            ->set('searchTerm', 'Imperial')
            ->assertSeeText('Imperial Club')
            ->assertDontSeeText('Alex Carter')
            ->assertSeeText('Team');
    }
}
