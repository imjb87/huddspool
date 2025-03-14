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

        view()->share('season', Season::current());

        Vite::useScriptTagAttributes([
            'defer' => true, // Specify an attribute without a value...
        ]);

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });        
    }
}
