<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FixtureResultBroadcastChannelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
        config(['broadcasting.default' => 'reverb']);
    }

    public function test_authorized_team_admin_can_join_fixture_result_presence_channel(): void
    {
        ['fixture' => $fixture, 'teamAdmin' => $teamAdmin] = $this->createFixtureContext();

        $response = $this->actingAs($teamAdmin)
            ->post('/broadcasting/auth', [
                'socket_id' => '1234.5678',
                'channel_name' => 'fixture-results.'.$fixture->id,
            ])
            ->assertOk();

        $payload = json_decode($response->getContent(), true);

        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('auth', $payload);
        $this->assertArrayHasKey('channel_data', $payload);

        $channelData = json_decode($payload['channel_data'], true);

        $this->assertIsArray($channelData);
        $this->assertSame($teamAdmin->name, $channelData['user_info']['name']);
        $this->assertSame($teamAdmin->avatar_url, $channelData['user_info']['avatar_url']);
    }

    public function test_unauthorized_user_cannot_join_fixture_result_presence_channel(): void
    {
        ['fixture' => $fixture] = $this->createFixtureContext();

        $outsider = User::factory()->create([
            'role' => 2,
            'is_admin' => false,
        ]);

        $response = $this->actingAs($outsider)
            ->post('/broadcasting/auth', [
                'socket_id' => '1234.5678',
                'channel_name' => 'fixture-results.'.$fixture->id,
            ])
            ->assertForbidden();
    }

    /**
     * @return array{fixture: Fixture, teamAdmin: User}
     */
    private function createFixtureContext(): array
    {
        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now()->subDay(),
        ]);

        $teamAdmin = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => 2,
            'is_admin' => false,
        ]);

        return compact('fixture', 'teamAdmin');
    }
}
