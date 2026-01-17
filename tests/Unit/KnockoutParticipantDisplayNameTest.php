<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutParticipant;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnockoutParticipantDisplayNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_doubles_with_missing_second_player_shows_tbc_even_with_label(): void
    {
        $season = Season::factory()->create();
        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Test Doubles Knockout',
            'type' => KnockoutType::Doubles,
        ]);
        $playerOne = User::factory()->create(['name' => 'Alex One']);

        $participant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Seed 1',
            'player_one_id' => $playerOne->id,
        ]);

        $this->assertSame('Alex One & TBC', $participant->display_name);
    }

    public function test_doubles_with_both_players_prefers_label_when_present(): void
    {
        $season = Season::factory()->create();
        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Another Doubles Knockout',
            'type' => KnockoutType::Doubles,
        ]);
        $playerOne = User::factory()->create(['name' => 'Alex One']);
        $playerTwo = User::factory()->create(['name' => 'Alex Two']);

        $participant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'label' => 'Seed 2',
            'player_one_id' => $playerOne->id,
            'player_two_id' => $playerTwo->id,
        ]);

        $this->assertSame('Seed 2', $participant->display_name);
    }
}
