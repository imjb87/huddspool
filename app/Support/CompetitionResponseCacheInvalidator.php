<?php

namespace App\Support;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Spatie\ResponseCache\Facades\ResponseCache;

class CompetitionResponseCacheInvalidator
{
    public function forgetRulesetContent(?string $slug): void
    {
        $this->clear(ResponseCacheTagSet::rulesetContent(), $this->fallbackRulesetPaths($slug));
    }

    public function forgetResultContent(): void
    {
        $this->clear(ResponseCacheTagSet::resultContent());
    }

    public function forgetFrameContent(): void
    {
        $this->clear(ResponseCacheTagSet::frameContent());
    }

    public function forgetSectionContent(): void
    {
        $this->clear(ResponseCacheTagSet::sectionContent());
    }

    public function forgetExpulsionContent(): void
    {
        $this->clear(ResponseCacheTagSet::expulsionContent());
    }

    public function forgetSeasonContent(): void
    {
        $this->clear(ResponseCacheTagSet::seasonContent());
    }

    public function forgetTeamContent(): void
    {
        $this->clear(ResponseCacheTagSet::teamContent());
    }

    public function forgetUserContent(): void
    {
        $this->clear(ResponseCacheTagSet::userContent());
    }

    public function forgetHomeContent(): void
    {
        $this->clear([ResponseCacheTags::HOME]);
    }

    public function forgetVenueContent(): void
    {
        $this->clear(ResponseCacheTagSet::venueContent());
    }

    public function forgetKnockoutContent(): void
    {
        $this->clear(ResponseCacheTagSet::knockoutContent());
    }

    public function forgetPageContent(): void
    {
        $this->clear([ResponseCacheTags::PAGES]);
    }

    /**
     * @param  array<int, string>  $tags
     * @param  array<int, string>  $fallbackPaths
     */
    private function clear(array $tags, array $fallbackPaths = ['/']): void
    {
        if ($this->supportsTaggedResponseCache()) {
            ResponseCache::clear($tags);

            return;
        }

        ResponseCache::forget($fallbackPaths);
    }

    /**
     * @return array<int, string>
     */
    private function fallbackRulesetPaths(?string $slug): array
    {
        if (blank($slug)) {
            return ['/'];
        }

        return [
            '/',
            "/{$slug}",
            "/rulesets/{$slug}",
            "/tables/{$slug}/",
            "/fixtures-and-results/{$slug}/",
            "/players/averages/{$slug}/",
        ];
    }

    private function supportsTaggedResponseCache(): bool
    {
        $store = Cache::store(config('responsecache.cache.store'))->getStore();

        return $store instanceof TaggableStore && filled(config('responsecache.cache.tag'));
    }
}
