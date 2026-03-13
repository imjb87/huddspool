<?php

namespace App\Providers;

use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\Season;
use App\Models\Venue;
use App\Observers\KnockoutRoundObserver;
use App\Observers\NewsObserver;
use App\Observers\SeasonObserver;
use App\Observers\VenueObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        KnockoutRound::observe(KnockoutRoundObserver::class);
        News::observe(NewsObserver::class);
        Season::observe(SeasonObserver::class);
        Venue::observe(VenueObserver::class);

        Vite::useScriptTagAttributes([
            'defer' => true,
        ]);

        Pulse::user(fn ($user) => [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar_url,
        ]);
    }
}
