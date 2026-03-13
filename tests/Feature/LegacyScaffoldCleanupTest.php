<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_invite_registration_route_still_renders_the_registration_view(): void
    {
        $user = User::factory()->create([
            'invitation_token' => 'invite-token',
        ]);

        $this->get(route('invite.register', $user->invitation_token))
            ->assertOk()
            ->assertSeeText('Set password');
    }
}
