<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Knockout;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Observers\SeasonObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();

        Season::observe(SeasonObserver::class);

        if (Schema::hasTable('rulesets')) {
            $rulesets = Cache::remember('nav:rulesets', now()->addMinutes(10), function () {
                return Ruleset::all();
            });
            view()->share('rulesets', $rulesets);
        } else {
            view()->share('rulesets', []);
        }

        if (Schema::hasTable('seasons')) {
            $past_seasons = Cache::remember('nav:past-seasons', now()->addMinutes(10), function () {
                return Season::query()
                    ->where('is_open', false)
                    ->with(['sections.ruleset:id,name,slug'])
                    ->orderByDesc('id')
                    ->get();
            });
            view()->share('past_seasons', $past_seasons);
        } else {
            view()->share('past_seasons', []);
        }

        Vite::useScriptTagAttributes([
            'defer' => true, // Specify an attribute without a value...
        ]);

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });        
    }
}
