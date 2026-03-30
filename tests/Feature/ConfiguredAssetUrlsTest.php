<?php

namespace Tests\Feature;

use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\TestCase;

class ConfiguredAssetUrlsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        ResponseCache::clear();
    }

    public function test_home_page_does_not_render_removed_optional_tracking_scripts(): void
    {
        config([
            'services.google_analytics.measurement_id' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('https://www.googletagmanager.com/gtag/js', false)
            ->assertDontSee('https://static.hotjar.com/c/hotjar-', false)
            ->assertDontSee('kit.fontawesome.com', false);
    }

    public function test_home_page_defers_google_analytics_loading_until_after_initial_render(): void
    {
        config([
            'services.google_analytics.measurement_id' => 'G-620MNWY15S',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-google-analytics-measurement-id="G-620MNWY15S"', false)
            ->assertDontSee('https://www.googletagmanager.com/gtag/js?id=G-620MNWY15S', false)
            ->assertDontSee("gtag('config'", false);
    }

    public function test_venue_page_hides_google_map_embed_when_key_is_not_configured(): void
    {
        config([
            'services.google_maps.embed_key' => null,
        ]);

        $venue = Venue::factory()->create([
            'name' => 'The Duke',
            'address' => '1 High Street, Huddersfield',
        ]);

        $this->get(route('venue.show', $venue))
            ->assertOk()
            ->assertSeeText('Map embedding is not configured right now.')
            ->assertDontSee('https://www.google.com/maps/embed/v1/place', false);
    }
}
