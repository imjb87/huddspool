<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Ruleset;
use Illuminate\Support\Facades\Vite;

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
        $rulesets = Ruleset::all();
        view()->share('rulesets', $rulesets);

        Vite::useScriptTagAttributes([
            'defer' => true, // Specify an attribute without a value...
        ]);        
    }
}
