<?php

namespace App\Support;

use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;

final class PublicResponseCacheProfile extends CacheAllSuccessfulGetRequests
{
    /**
     * @var list<string>
     */
    private const CACHEABLE_ROUTES = [
        'ruleset.show',
        'ruleset.rules',
        'fixture.show',
        'fixture.download',
        'result.show',
        'result.og-image',
        'player.show',
        'team.show',
        'history.section.show',
        'history.knockout.show',
        'venue.show',
        'knockout.index',
        'knockout.show',
        'ruleset.section.show',
        'page.show',
    ];

    public function shouldCacheRequest(Request $request): bool
    {
        return parent::shouldCacheRequest($request)
            && $request->routeIs(self::CACHEABLE_ROUTES);
    }
}
