<?php

namespace Tests\Feature;

use App\Filament\Resources\SeasonResource\Pages\CreateSeason;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SeasonAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_season_from_filament(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(CreateSeason::class)
            ->fillForm([
                'name' => 'August 2026',
                'team_entry_fee' => 0,
                'signup_opens_at' => null,
                'signup_closes_at' => null,
                'dates' => collect(range(0, 17))
                    ->map(fn (int $week): array => ['date' => now()->startOfDay()->addWeeks($week)->toDateString()])
                    ->all(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('seasons', [
            'name' => 'August 2026',
            'slug' => 'august-2026',
        ]);
    }
}
