<?php

namespace Tests\Unit;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutParticipant;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnockoutParticipantSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_for_team_knockout_matches_team_name(): void
    {
        $season = Season::factory()->create();
        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'EPA Team Knockout',
            'type' => KnockoutType::Team,
        ]);
        $team = Team::factory()->create(['name' => 'Epa Team A']);

        $participant = KnockoutParticipant::create([
            'knockout_id' => $knockout->id,
            'team_id' => $team->id,
        ]);

        $results = KnockoutParticipant::query()
            ->searchForKnockout($knockout, 'Epa')
            ->orderBy('knockout_participants.seed')
            ->orderBy('knockout_participants.label')
            ->get();

        $this->assertCount(1, $results);
        $this->assertSame($participant->id, $results->first()->id);
    }
}
