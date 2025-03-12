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

        if(Schema::hasTable('knockouts')) {
            // get all knockouts for the current season
            $knockouts = Knockout::where('season_id', Season::where('is_open', 1)->first()->id)->get();
            view()->share('knockouts', $knockouts);
        } else {
            view()->share('knockouts', []);
        }

        Vite::useScriptTagAttributes([
            'defer' => true, // Specify an attribute without a value...
        ]);

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });        
    }
}
