<?php

namespace Tests\Feature;

use App\Models\Fixture;
use App\Models\Knockout;
use App\Models\Page;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SitemapGenerateCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_generate_command_writes_expected_public_urls(): void
    {
        config()->set('app.frontend_url', 'https://example.com');

        $originalPublicPath = public_path();
        $temporaryPublicPath = storage_path('framework/testing/sitemap-public');
        File::deleteDirectory($temporaryPublicPath);
        File::ensureDirectoryExists($temporaryPublicPath);
        app()->usePublicPath($temporaryPublicPath);

        try {
            $openSeason = Season::factory()->create([
                'name' => 'Summer 2026',
                'slug' => 'summer-2026',
                'is_open' => true,
            ]);
            $historySeason = Season::factory()->create([
                'name' => 'Winter 2025',
                'slug' => 'winter-2025',
                'is_open' => false,
            ]);

            $ruleset = Ruleset::factory()->create([
                'name' => 'International Rules',
                'slug' => 'international-rules',
            ]);

            $openSection = Section::factory()->create([
                'name' => 'Premier Division',
                'slug' => 'premier-division',
                'season_id' => $openSeason->id,
                'ruleset_id' => $ruleset->id,
            ]);
            $historySection = Section::factory()->create([
                'name' => 'Championship Division',
                'slug' => 'championship-division',
                'season_id' => $historySeason->id,
                'ruleset_id' => $ruleset->id,
            ]);

            $pageTimestamp = Carbon::create(2026, 3, 25, 12, 0, 0, 'UTC');

            $page = Page::query()->forceCreate([
                'title' => 'Handbook',
                'slug' => 'handbook',
                'content' => '<p>League handbook.</p>',
                'created_at' => $pageTimestamp,
                'updated_at' => $pageTimestamp,
            ]);

            $currentKnockout = Knockout::factory()->create([
                'season_id' => $openSeason->id,
                'name' => 'Champion of Champions',
                'slug' => 'champion-of-champions',
            ]);
            $historicalKnockout = Knockout::factory()->create([
                'season_id' => $historySeason->id,
                'name' => 'Winter Cup',
                'slug' => 'winter-cup',
            ]);

            $venue = Venue::factory()->create([
                'name' => 'The Victory Club',
            ]);

            $homeTeam = Team::factory()->create([
                'name' => 'Break Masters',
                'venue_id' => $venue->id,
            ]);
            $awayTeam = Team::factory()->create([
                'name' => 'Cue Kings',
                'venue_id' => $venue->id,
            ]);

            $openSection->teams()->attach($homeTeam->id, ['sort' => 1]);
            $openSection->teams()->attach($awayTeam->id, ['sort' => 2]);

            $player = User::factory()->create([
                'team_id' => $homeTeam->id,
            ]);

            $fixture = Fixture::factory()->create([
                'season_id' => $openSeason->id,
                'section_id' => $openSection->id,
                'ruleset_id' => $ruleset->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'venue_id' => $venue->id,
            ]);

            $resultTimestamp = Carbon::create(2026, 3, 25, 16, 30, 0, 'UTC');

            $result = Result::factory()->create([
                'fixture_id' => $fixture->id,
                'home_team_id' => $homeTeam->id,
                'home_team_name' => $homeTeam->name,
                'away_team_id' => $awayTeam->id,
                'away_team_name' => $awayTeam->name,
                'section_id' => $openSection->id,
                'ruleset_id' => $ruleset->id,
                'submitted_by' => $player->id,
                'submitted_at' => $resultTimestamp,
                'created_at' => $resultTimestamp,
                'updated_at' => $resultTimestamp,
            ]);

            $this->artisan('sitemap:generate')
                ->expectsOutputToContain('Sitemap generated:')
                ->assertExitCode(0);

            $sitemapPath = public_path('sitemap.xml');

            $this->assertFileExists($sitemapPath);

            $xml = file_get_contents($sitemapPath);

            $this->assertIsString($xml);
            $this->assertStringContainsString($this->absoluteUrl('/'), $xml);
            $this->assertStringContainsString($this->absoluteRoute('history.index'), $xml);
            $this->assertStringContainsString($this->absoluteRoute('ruleset.show', $ruleset), $xml);
            $this->assertStringContainsString($this->absoluteRoute('ruleset.section.show', [
                'ruleset' => $ruleset,
                'section' => $openSection,
            ]), $xml);
            $this->assertStringContainsString($this->absoluteRoute('history.section.show', [
                'season' => $historySeason,
                'ruleset' => $ruleset,
                'section' => $historySection->slug,
            ]), $xml);
            $this->assertStringNotContainsString(
                $this->absoluteUrl("/history/{$historySeason->slug}/{$ruleset->slug}/{$historySection->slug}"),
                $xml
            );
            $this->assertStringContainsString($this->absoluteRoute('page.show', $page), $xml);
            $this->assertStringContainsString($this->absoluteRoute('knockout.show', $currentKnockout), $xml);
            $this->assertStringContainsString($this->absoluteRoute('history.knockout.show', [
                'season' => $historySeason,
                'knockout' => $historicalKnockout,
            ]), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('knockout.show', $historicalKnockout), $xml);
            $this->assertStringContainsString($this->absoluteRoute('team.show', $homeTeam), $xml);
            $this->assertStringContainsString($this->absoluteRoute('player.show', ['player' => $player]), $xml);
            $this->assertStringContainsString($this->absoluteRoute('venue.show', $venue), $xml);
            $this->assertStringContainsString($this->absoluteRoute('fixture.show', $fixture), $xml);
            $this->assertStringContainsString($this->absoluteRoute('result.show', $result), $xml);
            $this->assertStringContainsString('<lastmod>'.$pageTimestamp->toAtomString().'</lastmod>', $xml);
            $this->assertStringContainsString('<lastmod>'.$resultTimestamp->toAtomString().'</lastmod>', $xml);

            $this->assertStringNotContainsString($this->absoluteRoute('account.show'), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('login'), $xml);
            $this->assertStringNotContainsString($this->absoluteUrl('/register/'), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('password.request'), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('support.tickets'), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('fixture.download', [
                'ruleset' => $ruleset,
                'section' => $openSection,
            ]), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('laravelpwa.manifest'), $xml);
            $this->assertStringNotContainsString($this->absoluteRoute('laravelpwa.offline'), $xml);
        } finally {
            app()->usePublicPath($originalPublicPath);
            File::deleteDirectory($temporaryPublicPath);
        }
    }

    public function test_schedule_lists_daily_sitemap_generation(): void
    {
        $events = app(Schedule::class)->events();
        $sitemapEvent = collect($events)->first(fn ($event) => str_contains((string) $event->command, 'sitemap:generate'));

        $this->assertNotNull($sitemapEvent);
        $this->assertSame('0 0 * * *', $sitemapEvent->expression);
    }

    /**
     * @param  array<string, mixed>|object|string|null  $parameters
     */
    private function absoluteRoute(string $name, array|object|string|null $parameters = null): string
    {
        $path = route($name, $parameters ?? [], false);

        return $this->absoluteUrl($path);
    }

    private function absoluteUrl(string $path): string
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        return $path === '/'
            ? "{$frontendUrl}/"
            : $frontendUrl.'/'.ltrim($path, '/');
    }
}
