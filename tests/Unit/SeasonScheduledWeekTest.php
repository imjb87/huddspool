<?php

namespace Tests\Unit;

use App\Models\Season;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SeasonScheduledWeekTest extends TestCase
{
    public function test_current_or_previous_scheduled_week_uses_exact_matching_week(): void
    {
        $season = new Season([
            'dates' => [
                '2026-03-31',
                '2026-04-07',
                '2026-04-14',
            ],
        ]);

        $this->assertSame(2, $season->currentOrPreviousScheduledWeek(Carbon::parse('2026-04-08')));
    }

    public function test_current_or_previous_scheduled_week_falls_back_to_previous_scheduled_week(): void
    {
        $season = new Season([
            'dates' => [
                '2026-03-31',
                '2026-04-14',
            ],
        ]);

        $this->assertSame(1, $season->currentOrPreviousScheduledWeek(Carbon::parse('2026-04-06')));
    }

    public function test_current_or_previous_scheduled_week_uses_first_week_before_the_season_starts(): void
    {
        $season = new Season([
            'dates' => [
                '2026-04-14',
                '2026-04-21',
            ],
        ]);

        $this->assertSame(1, $season->currentOrPreviousScheduledWeek(Carbon::parse('2026-04-06')));
    }
}
