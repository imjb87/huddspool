<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\KnockoutMatch;
use App\Observers\KnockoutMatchObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Database\Eloquent\Model;

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

        KnockoutMatch::observe(KnockoutMatchObserver::class);

        if(Schema::hasTable('rulesets')) {
            $rulesets = Ruleset::all();
            view()->share('rulesets', $rulesets);
        } else {
            view()->share('rulesets', []);
        }

        if(Schema::hasTable('seasons')) {
            $past_seasons = Season::where('is_open', 0)->orderBy('id', 'desc')->get();
            view()->share('past_seasons', $past_seasons);
        } else {
            view()->share('past_seasons', []);
        }

        Vite::useScriptTagAttributes([
            'defer' => true, // Specify an attribute without a value...
        ]);
    }
}
