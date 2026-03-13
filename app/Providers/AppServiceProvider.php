<?php

namespace App\Providers;

use App\Models\Season;
use App\Models\Venue;
use App\Observers\SeasonObserver;
use App\Observers\VenueObserver;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();

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
