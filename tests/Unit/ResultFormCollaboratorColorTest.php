<?php

namespace Tests\Unit;

use App\Support\ResultFormCollaboratorColor;
use PHPUnit\Framework\TestCase;

class ResultFormCollaboratorColorTest extends TestCase
{
    public function test_a_user_keeps_the_same_collaborator_colour(): void
    {
        $this->assertSame(
            ResultFormCollaboratorColor::forUser(12),
            ResultFormCollaboratorColor::forUser(12),
        );
        $this->assertNotSame(
            ResultFormCollaboratorColor::forUser(1),
            ResultFormCollaboratorColor::forUser(9),
        );
        $this->assertNotSame(
            ResultFormCollaboratorColor::forUser(1),
            ResultFormCollaboratorColor::forUser(2),
        );
    }

    public function test_collaborator_colours_are_valid_hex_values(): void
    {
        foreach ([1, 2, 3, 8, 9, 16] as $userId) {
            $this->assertMatchesRegularExpression(
                '/^#[0-9a-f]{6}$/i',
                ResultFormCollaboratorColor::forUser($userId),
            );
        }
    }

    public function test_roster_assignment_uses_distinct_slots_for_gapped_user_ids(): void
    {
        $userIds = [4, 12, 27, 41];
        $colours = array_map(
            fn (int $userId): string => ResultFormCollaboratorColor::forRoster($userIds, $userId),
            $userIds,
        );

        $this->assertCount(count($userIds), array_unique($colours));
        $this->assertSame(
            ResultFormCollaboratorColor::palette()[0],
            ResultFormCollaboratorColor::forRoster($userIds, 4),
        );
        $this->assertSame(
            ResultFormCollaboratorColor::palette()[1],
            ResultFormCollaboratorColor::forRoster($userIds, 12),
        );
    }

    public function test_roster_assignment_remains_unique_beyond_the_base_palette(): void
    {
        $userIds = [4, 12, 27, 41, 52, 63, 74, 85, 96, 107];
        $colours = array_map(
            fn (int $userId): string => ResultFormCollaboratorColor::forRoster($userIds, $userId),
            $userIds,
        );

        $this->assertCount(count($userIds), array_unique($colours));
        foreach ($colours as $colour) {
            $this->assertIsString($colour);
        }
    }
}
