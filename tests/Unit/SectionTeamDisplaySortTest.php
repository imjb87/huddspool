<?php

namespace Tests\Unit;

use App\Models\SectionTeam;
use Tests\TestCase;

class SectionTeamDisplaySortTest extends TestCase
{
    public function test_display_sort_wraps_ten_to_zero(): void
    {
        $this->assertSame(0, SectionTeam::displaySortValue(10));
    }

    public function test_display_sort_leaves_other_positions_unchanged(): void
    {
        $this->assertSame(4, SectionTeam::displaySortValue(4));
    }
}
