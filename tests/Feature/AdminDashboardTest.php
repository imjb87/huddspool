<?php

namespace Tests\Feature;

use App\Filament\Widgets\SeasonStatsChart;
use App\Filament\Widgets\UserStatsOverview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_updated_widgets(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSeeLivewire(UserStatsOverview::class);
        $response->assertSeeLivewire(SeasonStatsChart::class);
        $response->assertSee('filament-copilot-button', false);
        $response->assertSee('filament-copilot-chat', false);
        $response->assertSee('dispatchLivewire(name, params = {}, bubbles = true)', false);
        $response->assertSee('await $wire.handleStreamComplete(', false);
    }
}
