<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_send_the_standard_password_reset_link_from_the_user_edit_page(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->unverified()->create();

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditUser::class, [
                'record' => $user->getRouteKey(),
            ])
            ->callAction('passwordReset');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_resetting_a_new_users_password_verifies_their_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create([
            'password' => Hash::make('old-password'),
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $response = $this->post(route('password.store'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertNotNull($user->email_verified_at);
    }
}
