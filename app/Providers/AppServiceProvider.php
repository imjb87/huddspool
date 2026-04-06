<?php

namespace App\Providers;

use App\Http\Controllers\Filament\CopilotStreamController;
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
use App\Support\Copilot\Tools\ListPagesTool as AppListPagesTool;
use App\Support\Copilot\Tools\ListResourcesTool as AppListResourcesTool;
use App\Support\Copilot\Tools\ListWidgetsTool as AppListWidgetsTool;
use App\Support\Copilot\Tools\RecallTool as AppRecallTool;
use App\Support\Copilot\Tools\RunToolTool as AppRunToolTool;
use EslamRedaDiv\FilamentCopilot\Http\Controllers\StreamController as PackageCopilotStreamController;
use EslamRedaDiv\FilamentCopilot\Tools\ListPagesTool as PackageListPagesTool;
use EslamRedaDiv\FilamentCopilot\Tools\ListResourcesTool as PackageListResourcesTool;
use EslamRedaDiv\FilamentCopilot\Tools\ListWidgetsTool as PackageListWidgetsTool;
use EslamRedaDiv\FilamentCopilot\Tools\RecallTool as PackageRecallTool;
use EslamRedaDiv\FilamentCopilot\Tools\RunToolTool as PackageRunToolTool;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PackageCopilotStreamController::class, CopilotStreamController::class);
        $this->app->bind(PackageListResourcesTool::class, AppListResourcesTool::class);
        $this->app->bind(PackageListPagesTool::class, AppListPagesTool::class);
        $this->app->bind(PackageListWidgetsTool::class, AppListWidgetsTool::class);
        $this->app->bind(PackageRecallTool::class, AppRecallTool::class);
        $this->app->bind(PackageRunToolTool::class, AppRunToolTool::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('brevo', function (): mixed {
            return (new BrevoTransportFactory)->create(
                new Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key'),
                )
            );
        });

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
