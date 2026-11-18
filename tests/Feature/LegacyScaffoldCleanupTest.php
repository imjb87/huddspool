<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegacyScaffoldCleanupTest extends TestCase
{
    public function test_legacy_profile_admin_and_open_registration_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('profile.edit'));
        $this->assertFalse(Route::has('profile.update'));
        $this->assertFalse(Route::has('profile.destroy'));
        $this->assertFalse(Route::has('admin.dashboard'));
        $this->assertFalse(Route::has('register'));
    }

    public function test_invite_registration_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('invite.register'));
        $this->assertFalse(Route::has('invite.store'));
    }
}
