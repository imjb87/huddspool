<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfiguredAssetUrlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_uses_configured_font_awesome_kit_url(): void
    {
        config([
            'services.font_awesome.kit_url' => 'https://kit.fontawesome.com/configured-home.js',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://kit.fontawesome.com/configured-home.js', false);
    }

    public function test_login_page_uses_configured_font_awesome_kit_url(): void
    {
        config([
            'services.font_awesome.kit_url' => 'https://kit.fontawesome.com/configured-login.js',
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('https://kit.fontawesome.com/configured-login.js', false);
    }

    public function test_player_profile_uses_configured_font_awesome_kit_url(): void
    {
        config([
            'services.font_awesome.kit_url' => 'https://kit.fontawesome.com/configured-player.js',
        ]);

        $player = User::factory()->create();

        $this->get(route('player.show', $player))
            ->assertOk()
            ->assertSee('https://kit.fontawesome.com/configured-player.js', false);
    }

    public function test_home_page_does_not_render_optional_tracking_scripts_when_not_configured(): void
    {
        config([
            'services.google_analytics.measurement_id' => null,
            'services.hotjar.site_id' => null,
            'services.hotjar.snippet_version' => null,
            'services.font_awesome.kit_url' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('https://www.googletagmanager.com/gtag/js', false)
            ->assertDontSee('https://static.hotjar.com/c/hotjar-', false)
            ->assertDontSee('kit.fontawesome.com', false);
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
