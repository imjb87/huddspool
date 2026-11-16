<?php

namespace Tests\Unit;

use App\Support\ResultFrameState;
use PHPUnit\Framework\TestCase;

class ResultFrameStateTest extends TestCase
{
    public function test_normalize_produces_the_canonical_ten_frame_shape(): void
    {
        $state = ResultFrameState::normalize([
            '1' => [
                'home_player_id' => '12',
                'away_player_id' => 34,
                'home_score' => '1',
                'away_score' => 0,
            ],
        ]);

        $this->assertCount(10, $state);
        $this->assertSame([
            'home_player_id' => 12,
            'away_player_id' => 34,
            'home_score' => 1,
            'away_score' => 0,
        ], $state[1]);
        $this->assertSame([
            'home_player_id' => null,
            'away_player_id' => null,
            'home_score' => 0,
            'away_score' => 0,
        ], $state[2]);
    }

    public function test_completed_and_score_helpers_share_the_same_frame_rules(): void
    {
        $frames = [
            1 => [
                'home_player_id' => 12,
                'away_player_id' => 34,
                'home_score' => 1,
                'away_score' => 0,
            ],
            2 => [
                'home_player_id' => 12,
                'away_player_id' => 34,
                'home_score' => 0,
                'away_score' => 0,
            ],
        ];

        $this->assertCount(1, ResultFrameState::completed($frames));
        $this->assertSame([
            'home_score' => 1,
            'away_score' => 0,
            'total_score' => 1,
        ], ResultFrameState::scores($frames));
    }

    public function test_changed_frame_numbers_ignore_key_and_scalar_formatting_differences(): void
    {
        $this->assertSame(
            [2],
            ResultFrameState::changedFrameNumbers(
                [1 => ['home_player_id' => '12', 'away_player_id' => '34', 'home_score' => '1', 'away_score' => '0']],
                [
                    1 => ['home_player_id' => 12, 'away_player_id' => 34, 'home_score' => 1, 'away_score' => 0],
                    2 => ['home_player_id' => 12, 'away_player_id' => 34, 'home_score' => 0, 'away_score' => 1],
                ],
            ),
        );
    }
}
