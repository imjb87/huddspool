<?php

namespace Tests\Feature;

use App\Models\User;
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
}
