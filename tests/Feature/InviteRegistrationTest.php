<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\InviteController;
use App\Models\User;
use App\Notifications\InviteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InviteRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invited_user_can_view_registration_form(): void
    {
        $user = User::factory()->unverified()->create([
            'invitation_token' => 'valid-invitation-token',
        ]);

        $response = $this->get(route('invite.register', $user->invitation_token));

        $response->assertOk();
        $response->assertSee('data-invite-register-page', false);
        $response->assertSeeText('Set password');
        $response->assertSeeText($user->email);
    }

    public function test_invited_user_can_complete_registration(): void
    {
        $user = User::factory()->unverified()->create([
            'invitation_token' => 'valid-invitation-token',
        ]);

        $response = $this->post(route('invite.store', $user->invitation_token), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Account created successfully! You can now log in.');

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->invitation_token);
    }

    public function test_invite_registration_requires_a_confirmed_password(): void
    {
        $user = User::factory()->unverified()->create([
            'invitation_token' => 'another-valid-token',
        ]);

        $response = $this
            ->from(route('invite.register', $user->invitation_token))
            ->post(route('invite.store', $user->invitation_token), [
                'password' => 'short',
                'password_confirmation' => 'different',
            ]);

        $response
            ->assertRedirect(route('invite.register', $user->invitation_token))
            ->assertSessionHasErrors(['password']);

        $user->refresh();

        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertNull($user->email_verified_at);
        $this->assertSame('another-valid-token', $user->invitation_token);
    }

    public function test_invalid_invitation_token_returns_not_found_before_validation(): void
    {
        $response = $this->post(route('invite.store', 'missing-token'), []);

        $response->assertNotFound();
    }

    public function test_sending_an_invite_queues_the_email_notification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'invitation_token' => null,
        ]);

        InviteController::send($user);

        $user->refresh();

        $this->assertNotNull($user->invitation_token);

        Notification::assertSentTo(
            $user,
            InviteNotification::class,
            function (InviteNotification $notification, array $channels) {
                return $notification instanceof ShouldQueue
                    && in_array('mail', $channels, true)
                    && $notification->queue === 'emails';
            }
        );
    }
}
