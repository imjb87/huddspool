<?php

namespace Tests\Unit;

use App\Domain\Authorization\SiteAuthorization;
use App\Enums\RoleName;
use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exposes_role_labels_and_options(): void
    {
        $this->assertSame('Player', UserRole::Player->label());
        $this->assertSame('Team Admin', UserRole::TeamAdmin->label());
        $this->assertSame([
            UserRole::Player->value => 'Player',
            UserRole::TeamAdmin->value => 'Team Admin',
        ], UserRole::options());
    }

    public function test_user_role_helpers_use_the_enum_values(): void
    {
        $teamAdmin = User::factory()->create([
            'role' => UserRole::TeamAdmin->value,
        ]);
        $player = User::factory()->create([
            'role' => UserRole::Player->value,
        ]);

        SiteAuthorization::assignRole($teamAdmin, RoleName::Admin);
        SiteAuthorization::assignRole($player, RoleName::Player);

        $teamAdmin->setRelation('team', new Team(['captain_id' => 99]));
        $player->setRelation('team', new Team(['captain_id' => 99]));

        $this->assertTrue($teamAdmin->isTeamAdmin());
        $this->assertTrue($teamAdmin->isAdmin());
        $this->assertTrue($teamAdmin->hasRole(RoleName::Admin->value));
        $this->assertSame('Admin', $teamAdmin->roleLabel());

        $this->assertFalse($player->isTeamAdmin());
        $this->assertFalse($player->isAdmin());
        $this->assertSame('Player', $player->roleLabel());
    }

    public function test_user_role_helpers_use_spatie_role_assignments_as_the_source_of_truth(): void
    {
        $user = User::factory()->create();

        SiteAuthorization::assignRole($user, RoleName::TeamAdmin);

        $user->refresh();

        $this->assertTrue($user->hasRole(RoleName::TeamAdmin->value));
        $this->assertTrue($user->isTeamAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertSame('Team Admin', $user->roleLabel());
    }
}
