<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
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
        $teamAdmin = new User([
            'id' => 1,
            'role' => UserRole::TeamAdmin->value,
            'is_admin' => true,
        ]);
        $player = new User([
            'id' => 2,
            'role' => UserRole::Player->value,
            'is_admin' => false,
        ]);

        $teamAdmin->setRelation('team', new Team(['captain_id' => 99]));
        $player->setRelation('team', new Team(['captain_id' => 99]));

        $this->assertTrue($teamAdmin->isTeamAdmin());
        $this->assertTrue($teamAdmin->isAdmin());
        $this->assertSame('Team Admin', $teamAdmin->roleLabel());

        $this->assertFalse($player->isTeamAdmin());
        $this->assertFalse($player->isAdmin());
        $this->assertSame('Player', $player->roleLabel());
    }
}
