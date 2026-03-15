<?php

namespace Tests\Feature;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SharedLayoutRouteLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_slug_routes_for_ruleset_links_in_shared_layouts(): void
    {
        Cache::flush();

        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create([
            'name' => 'World Rules',
            'slug' => 'world-rules',
        ]);
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
            'name' => 'World Rules Premier',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();

        $response->assertSee('href="'.route('ruleset.show', $ruleset).'"', false);
        $response->assertSee(route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]), false);
        $response->assertDontSee("/rulesets/{$ruleset->slug}/{$section->slug}", false);
        $response->assertDontSee("/rulesets/{$ruleset->slug}/{$section->id}", false);

        $response->assertDontSee('href="'.route('ruleset.index').'"', false);
        $response->assertDontSee(route('table.index', $ruleset), false);
        $response->assertDontSee(route('fixture.index', $ruleset), false);
        $response->assertDontSee(route('player.index', $ruleset), false);
        $response->assertDontSee('href="'.route('ruleset.show', $ruleset->id).'"', false);
    }
}
