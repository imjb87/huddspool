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

    public function test_component_opens_when_search_event_is_dispatched(): void
    {
        Livewire::test(Search::class)
            ->set('searchTerm', 'Imperial')
            ->dispatch('openSearch')
            ->assertSet('isOpen', true)
            ->assertSet('searchTerm', '');
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
            ->assertSee('data-search-modal-shell', false)
            ->assertSee('max-w-xl transform overflow-hidden rounded-xl', false)
            ->assertSee('data-search-loading-state', false)
            ->assertSee('data-search-loading-skeleton', false)
            ->assertSee('data-search-results-shell', false)
            ->assertSee('rounded-lg border border-transparent px-4 py-3', false)
            ->assertSeeText('Imperial Club')
            ->assertSeeText('12 West Street')
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
            ->assertSee('data-search-result-link', false);
    }

    public function test_component_shows_player_avatars_in_player_results(): void
    {
        Model::withoutEvents(function (): void {
            $season = Season::factory()->create(['is_open' => true]);
            $section = Section::factory()->create(['season_id' => $season->id]);
            $team = Team::factory()->create(['name' => 'Imperials']);

            $team->sections()->attach($section);

            User::factory()->create([
                'name' => 'Alex Carter',
                'team_id' => $team->id,
            ]);
        });

        Livewire::test(Search::class)
            ->set('searchTerm', 'Alex')
            ->assertSee('data-search-player-avatar', false)
            ->assertSeeText('Alex Carter')
            ->assertSeeText('Imperials');
    }

    public function test_component_limits_each_result_group_to_the_top_eight_matches(): void
    {
        Model::withoutEvents(function (): void {
            $season = Season::factory()->create(['is_open' => true]);
            $section = Section::factory()->create(['season_id' => $season->id]);

            foreach (range(1, 10) as $index) {
                $team = Team::factory()->create(['name' => sprintf('Imperial Team %02d', $index)]);
                $team->sections()->attach($section);

                User::factory()->create([
                    'name' => sprintf('Imperial Player %02d', $index),
                    'team_id' => $team->id,
                ]);

                Venue::factory()->create([
                    'name' => sprintf('Imperial Venue %02d', $index),
                    'address' => sprintf('%d West Street', $index),
                    'latitude' => 53.6486,
                    'longitude' => -1.7828,
                ]);
            }
        });

        Livewire::test(Search::class)
            ->set('searchTerm', 'Imperial')
            ->assertSeeText('Imperial Player 01')
            ->assertSeeText('Imperial Team 01')
            ->assertSeeText('Imperial Venue 01')
            ->assertDontSeeText('Imperial Player 09')
            ->assertDontSeeText('Imperial Team 09')
            ->assertDontSeeText('Imperial Venue 09');
    }
}
