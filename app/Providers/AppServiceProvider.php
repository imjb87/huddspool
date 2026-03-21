<?php

namespace App\Providers;

use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\Page;
use App\Models\Result;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Observers\KnockoutMatchObserver;
use App\Observers\KnockoutObserver;
use App\Observers\KnockoutParticipantObserver;
use App\Observers\KnockoutRoundObserver;
use App\Observers\NewsObserver;
use App\Observers\PageObserver;
use App\Observers\ResultObserver;
use App\Observers\SeasonObserver;
use App\Observers\TeamObserver;
use App\Observers\UserObserver;
use App\Observers\VenueObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        Knockout::observe(KnockoutObserver::class);
        KnockoutMatch::observe(KnockoutMatchObserver::class);
        KnockoutParticipant::observe(KnockoutParticipantObserver::class);
        KnockoutRound::observe(KnockoutRoundObserver::class);
        News::observe(NewsObserver::class);
        Page::observe(PageObserver::class);
        Result::observe(ResultObserver::class);
        Season::observe(SeasonObserver::class);
        Team::observe(TeamObserver::class);
        User::observe(UserObserver::class);
        Venue::observe(VenueObserver::class);

        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('support-tickets', function (Request $request): array {
            $email = strtolower((string) $request->input('email'));
            $key = $email ? $email.'|'.$request->ip() : $request->ip();

            return [
                Limit::perMinute(3)->by($key),
                Limit::perHour(15)->by($key),
            ];
        });

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
