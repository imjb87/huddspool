<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Enums\UserRole;
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
            'role' => UserRole::Player->value,
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
                'site_role' => RoleName::TeamAdmin->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $player->id,
            'role' => UserRole::TeamAdmin->value,
        ]);
        $this->assertTrue($player->fresh()->hasRole(RoleName::TeamAdmin->value));
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

    public function test_admin_can_impersonate_a_user_from_the_filament_edit_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $player = User::factory()->create();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditUser::class, [
                'record' => $player->getRouteKey(),
            ])
            ->callAction('impersonate')
            ->assertRedirect(route('account.show'));

        $this->assertAuthenticatedAs($player);
        $this->assertSame($admin->id, session('impersonated_by'));
    }

    public function test_filament_banner_uses_app_impersonation_leave_route(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $impersonatedAdmin = User::factory()->create([
            'is_admin' => true,
        ]);

        Filament::setCurrentPanel('admin');

        $this->actingAs($impersonatedAdmin);
        session([
            'impersonated_by' => $admin->id,
            'impersonator_guard' => 'web',
            'impersonator_guard_using' => 'web',
        ]);

        $response = $this->get(route('filament.admin.pages.dashboard'));

        $response->assertOk();
        $response->assertSee('href="'.route('impersonation.leave').'"', false);
        $response->assertDontSee('href="'.route('filament-impersonate.leave').'"', false);
    }
}
