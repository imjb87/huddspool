<?php

namespace Tests\Feature;

use App\Models\Ruleset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SharedLayoutRouteLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_slug_routes_for_ruleset_links_in_shared_layouts(): void
    {
        Cache::flush();

        $ruleset = Ruleset::factory()->create([
            'name' => 'World Rules',
            'slug' => 'world-rules',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();

        $response->assertSee(route('table.index', $ruleset), false);
        $response->assertSee(route('fixture.index', $ruleset), false);
        $response->assertSee(route('player.index', $ruleset), false);
        $response->assertSee(route('ruleset.show', $ruleset), false);

        $response->assertDontSee(route('table.index', $ruleset->id), false);
        $response->assertDontSee(route('fixture.index', $ruleset->id), false);
        $response->assertDontSee(route('player.index', $ruleset->id), false);
        $response->assertDontSee(route('ruleset.show', $ruleset->id), false);
    }
}
