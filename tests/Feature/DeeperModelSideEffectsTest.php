<?php

namespace Tests\Feature;

use App\KnockoutType;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\Season;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeeperModelSideEffectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_venue_is_geocoded_when_saved_with_an_address(): void
    {
        config([
            'services.nominatim.search_url' => 'https://example.com/search',
            'services.nominatim.user_agent' => 'Configured Geocoder',
        ]);

        Http::fake([
            'https://example.com/search*' => Http::response([
                ['lat' => '53.6458', 'lon' => '-1.7850'],
            ]),
        ]);

        $venue = Venue::factory()->create([
            'address' => 'Huddersfield Town Hall, HD1 2TA',
        ]);

        $this->assertSame(53.6458, $venue->latitude);
        $this->assertSame(-1.785, $venue->longitude);
    }

    public function test_venue_is_not_regeocoded_when_address_is_unchanged_and_coordinates_exist(): void
    {
        config([
            'services.nominatim.search_url' => 'https://example.com/search',
            'services.nominatim.user_agent' => 'Configured Geocoder',
        ]);

        Http::fake([
            'https://example.com/search*' => Http::response([
                ['lat' => '53.6458', 'lon' => '-1.7850'],
            ]),
        ]);

        $venue = Venue::factory()->create([
            'address' => 'Huddersfield Town Hall, HD1 2TA',
        ]);

        Http::fake();

        $venue->update([
            'telephone' => '01484 123456',
        ]);

        Http::assertNothingSent();
    }

    public function test_updating_round_date_updates_child_match_dates_and_preserves_time(): void
    {
        $season = Season::factory()->create();
        $knockout = Knockout::create([
            'season_id' => $season->id,
            'name' => 'Singles Cup',
            'type' => KnockoutType::Singles,
        ]);

        $round = KnockoutRound::create([
            'knockout_id' => $knockout->id,
            'name' => 'Quarter Final',
            'position' => 1,
            'scheduled_for' => '2026-03-01',
        ]);

        $match = KnockoutMatch::create([
            'knockout_id' => $knockout->id,
            'knockout_round_id' => $round->id,
            'position' => 1,
            'starts_at' => '2026-03-01 19:30:00',
        ]);

        $round->update([
            'scheduled_for' => '2026-03-08',
        ]);

        $this->assertSame('2026-03-08 19:30:00', $match->fresh()->starts_at?->format('Y-m-d H:i:s'));
    }

    public function test_creating_news_assigns_the_authenticated_user_as_author(): void
    {
        $author = User::factory()->create();

        $this->actingAs($author);

        $news = News::create([
            'title' => 'League update',
            'content' => 'Fixtures confirmed for next week.',
        ]);

        $this->assertSame($author->id, $news->author_id);
    }
}
