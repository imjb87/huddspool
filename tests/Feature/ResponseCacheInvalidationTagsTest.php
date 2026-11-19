<?php

namespace Tests\Feature;

use App\Domain\Competition\Cache\CompetitionResponseCacheInvalidator;
use App\Domain\Competition\Cache\ResponseCacheTags;
use Illuminate\Cache\Repository;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
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

        $this->tagAwareInvalidator()->forgetNewsContent();
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

        $this->tagAwareInvalidator()->forgetRulesetContent('international-rules');
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

        $this->tagAwareInvalidator()->forgetVenueContent();
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

        $this->tagAwareInvalidator()->forgetKnockoutContent();
        $this->addToAssertionCount(1);
    }

    public function test_page_invalidation_clears_only_the_pages_tag(): void
    {
        ResponseCache::shouldReceive('clear')
            ->once()
            ->with([ResponseCacheTags::PAGES]);

        $this->tagAwareInvalidator()->forgetPageContent();
        $this->addToAssertionCount(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    private function tagAwareInvalidator(): CompetitionResponseCacheInvalidator
    {
        $store = Mockery::mock(TaggableStore::class);
        $repository = Mockery::mock(Repository::class);

        $repository->shouldReceive('getStore')
            ->once()
            ->andReturn($store);

        Cache::shouldReceive('store')
            ->once()
            ->with(config('responsecache.cache.store'))
            ->andReturn($repository);

        config(['responsecache.cache.tag' => 'responses']);

        return new CompetitionResponseCacheInvalidator;
    }
}
