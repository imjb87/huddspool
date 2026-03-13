<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_the_admin_panel(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('filament.admin.pages.dashboard'))
            ->assertOk();
    }

    public function test_non_admin_is_redirected_away_from_the_admin_panel(): void
    {
        $player = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($player)
            ->get(route('filament.admin.pages.dashboard'))
            ->assertRedirect('/');
    }
}
