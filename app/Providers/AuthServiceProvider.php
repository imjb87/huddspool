<?php

namespace App\Providers;

use App\Models\Fixture;
use App\Models\KnockoutMatch;
use App\Models\Result;
use App\Models\User;
use App\Policies\FixturePolicy;
use App\Policies\KnockoutMatchPolicy;
use App\Policies\ResultPolicy;
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
        KnockoutMatch::class => KnockoutMatchPolicy::class,
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
