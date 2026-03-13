<?php

namespace Tests\Feature;

use App\Filament\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminMassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_users_role_from_the_filament_edit_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $player = User::factory()->create([
            'role' => '1',
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditUser::class, [
                'record' => $player->getRouteKey(),
            ])
            ->fillForm([
                'name' => $player->name,
                'email' => $player->email,
                'team_id' => $player->team_id,
                'telephone' => $player->telephone,
                'role' => '2',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $player->id,
            'role' => '2',
        ]);
    }

    public function test_admin_can_update_a_teams_captain_from_the_filament_edit_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $team = Team::factory()->create();
        $captain = User::factory()->create([
            'team_id' => $team->id,
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditTeam::class, [
                'record' => $team->getRouteKey(),
            ])
            ->fillForm([
                'name' => $team->name,
                'shortname' => $team->shortname,
                'venue_id' => $team->venue_id,
                'captain_id' => $captain->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'captain_id' => $captain->id,
        ]);
    }
}
