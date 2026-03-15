<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\Page;
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

    public function test_home_page_uses_knockout_show_and_knockout_dates_routes_in_shared_layouts(): void
    {
        Cache::flush();

        $season = Season::factory()->create(['is_open' => true]);
        $knockout = Knockout::query()->create([
            'season_id' => $season->id,
            'name' => 'Summer Singles',
            'slug' => 'summer-singles',
            'type' => KnockoutType::Singles->value,
        ]);

        Page::query()->create([
            'title' => 'Knockout Dates',
            'slug' => 'knockout-dates',
            'content' => '<p>Dates.</p>',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('knockout.show', $knockout).'"', false);
        $response->assertSee('href="'.route('page.show', 'knockout-dates').'"', false);
        $response->assertDontSee('href="'.route('knockout.index').'"', false);
    }
}
