<?php

namespace Tests\Feature;

use App\Models\Knockout;
use App\Models\Page;
use App\Models\Venue;
use App\Support\CompetitionCacheInvalidator;
use App\Support\ResponseCacheTags;
use Mockery;
use Spatie\ResponseCache\Facades\ResponseCache;
use Tests\TestCase;

class ResponseCacheInvalidationTagsTest extends TestCase
{
    public function test_news_invalidation_clears_only_the_home_tag(): void
    {
        ResponseCache::shouldReceive('clear')
            ->once()
            ->with([ResponseCacheTags::HOME, ResponseCacheTags::NEWS]);

        $this->tagAwareInvalidator()->forgetForNews();
        $this->addToAssertionCount(1);
    }

    public function test_ruleset_content_invalidation_clears_the_public_competition_tags(): void
    {
        ResponseCache::shouldReceive('clear')
            ->once()
            ->with([
                ResponseCacheTags::RULESETS,
                ResponseCacheTags::HISTORY,
                ResponseCacheTags::FIXTURES,
                ResponseCacheTags::TEAMS,
                ResponseCacheTags::VENUES,
            ]);

        $this->tagAwareInvalidator()->forgetForRulesetContent('international-rules', 1, 1, 1);
        $this->addToAssertionCount(1);
    }

    public function test_venue_invalidation_clears_only_venue_related_tags(): void
    {
        ResponseCache::shouldReceive('clear')
            ->once()
            ->with([
                ResponseCacheTags::FIXTURES,
                ResponseCacheTags::RESULTS,
                ResponseCacheTags::TEAMS,
                ResponseCacheTags::VENUES,
                ResponseCacheTags::KNOCKOUTS,
            ]);

        $this->tagAwareInvalidator()->forgetForVenue(new Venue);
        $this->addToAssertionCount(1);
    }

    public function test_knockout_invalidation_clears_knockout_related_tags(): void
    {
        ResponseCache::shouldReceive('clear')
            ->once()
            ->with([
                ResponseCacheTags::PLAYERS,
                ResponseCacheTags::TEAMS,
                ResponseCacheTags::KNOCKOUTS,
            ]);

        $this->tagAwareInvalidator()->forgetForKnockout(new Knockout);
        $this->addToAssertionCount(1);
    }

    public function test_page_invalidation_clears_only_the_pages_tag(): void
    {
        ResponseCache::shouldReceive('clear')
            ->once()
            ->with([ResponseCacheTags::PAGES]);

        $this->tagAwareInvalidator()->forgetForPage(new Page);
        $this->addToAssertionCount(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    private function tagAwareInvalidator(): CompetitionCacheInvalidator
    {
        $invalidator = Mockery::mock(CompetitionCacheInvalidator::class)->makePartial();
        $invalidator->shouldAllowMockingProtectedMethods();
        $invalidator->shouldReceive('supportsTaggedResponseCache')->andReturn(true);

        return $invalidator;
    }
}
