<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class InviteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_routes_are_no_longer_registered(): void
    {
        $this->assertFalse(Route::has('invite.register'));
        $this->assertFalse(Route::has('invite.store'));
    }
}
