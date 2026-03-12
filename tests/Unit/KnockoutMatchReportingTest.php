<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class KnockoutMatchReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_record_result_keeps_original_reporter_and_timestamp(): void
    {
        Carbon::setTestNow('2026-03-09 09:00:00');

        $season = Season::factory()->create();
        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Doubles KO',
            'type' => KnockoutType::Doubles,
            'best_of' => 7,
        ]);
        $round = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Final',
            'position' => 1,
        ]);

        $home = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Home Pair',
        ]);
        $away = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Away Pair',
        ]);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $home->id,
            'away_participant_id' => $away->id,
            'best_of' => 7,
        ]);

        $originalReporter = User::factory()->create();
        $editor = User::factory()->create();

        $match->recordResult(4, 2, $originalReporter);

        $originalReportedAt = $match->fresh()->reported_at;

        Carbon::setTestNow('2026-03-10 10:30:00');
        $match->refresh()->recordResult(4, 1, $editor);
        $match->refresh();

        $this->assertSame($originalReporter->id, $match->reported_by_id);
        $this->assertTrue($match->reported_at->equalTo($originalReportedAt));
        $this->assertSame('Submitted via frontend.', $match->report_reason);

        Carbon::setTestNow();
    }

    public function test_admin_edit_preserves_existing_reason(): void
    {
        $season = Season::factory()->create();
        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Doubles KO',
            'type' => KnockoutType::Doubles,
            'best_of' => 7,
        ]);
        $round = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Final',
            'position' => 1,
        ]);

        $home = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Home Pair',
        ]);
        $away = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Away Pair',
        ]);

        $reporter = User::factory()->create();

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'home_participant_id' => $home->id,
            'away_participant_id' => $away->id,
            'best_of' => 7,
            'home_score' => 4,
            'away_score' => 2,
            'reported_by_id' => $reporter->id,
            'reported_at' => now(),
            'report_reason' => 'Corrected after committee review.',
        ]);

        $match->update([
            'home_score' => 4,
            'away_score' => 1,
        ]);

        $this->assertSame('Corrected after committee review.', $match->fresh()->report_reason);
    }
}
