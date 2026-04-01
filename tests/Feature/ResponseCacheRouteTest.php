<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use Tests\TestCase;

class ResponseCacheRouteTest extends TestCase
{
    public function test_response_cache_uses_the_redis_store(): void
    {
        $this->assertSame('redis', config('responsecache.cache.store'));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function cacheableRouteNames(): array
    {
        return [
            'ruleset.show' => ['ruleset.show'],
            'fixture.show' => ['fixture.show'],
            'fixture.download' => ['fixture.download'],
            'result.show' => ['result.show'],
            'player.show' => ['player.show'],
            'team.show' => ['team.show'],
            'history.index' => ['history.index'],
            'history.section.show' => ['history.section.show'],
            'history.knockout.show' => ['history.knockout.show'],
            'venue.show' => ['venue.show'],
            'knockout.index' => ['knockout.index'],
            'knockout.show' => ['knockout.show'],
            'ruleset.section.show' => ['ruleset.section.show'],
            'page.show' => ['page.show'],
        ];
    }

    #[DataProvider('cacheableRouteNames')]
    public function test_public_routes_are_response_cached(string $routeName): void
    {
        $route = Route::getRoutes()->getByName($routeName);

        $this->assertNotNull($route);
        $this->assertTrue(
            collect($route->gatherMiddleware())->contains(
                fn (string $middleware): bool => str_starts_with($middleware, CacheResponse::class)
            ),
            "Expected route [{$routeName}] to use response cache middleware."
        );
    }

    public function test_authenticated_and_submission_routes_are_not_response_cached(): void
    {
        foreach (['home', 'account.show', 'account.team', 'result.create', 'support.tickets'] as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertFalse(
                collect($route->gatherMiddleware())->contains(
                    fn (string $middleware): bool => str_starts_with($middleware, CacheResponse::class)
                ),
                "Expected route [{$routeName}] to avoid response cache middleware."
            );
        }
    }
}
