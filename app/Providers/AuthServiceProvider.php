<?php

namespace App\Providers;

use App\Models\Fixture;
use App\Models\Result;
use App\Models\User;
use App\Policies\FixturePolicy;
use App\Policies\ResultPolicy;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Fixture::class => FixturePolicy::class,
        Result::class => ResultPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('viewPulse', function (User $user): bool {
            return $user->isAdmin();
        });
    }
}
