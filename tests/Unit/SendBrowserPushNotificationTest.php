<?php

namespace Tests\Unit;

use App\Jobs\SendBrowserPushNotification;
use App\Models\PushSubscription;
use App\Models\User;
use App\Support\Notifications\BrowserPushSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SendBrowserPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_delivery_updates_last_used_timestamp(): void
    {
        $user = User::factory()->create();
        $subscription = $user->pushSubscriptions()->create([
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
        ]);

        $sender = Mockery::mock(BrowserPushSender::class);
        $sender->shouldReceive('isConfigured')->once()->andReturn(true);
        $sender->shouldReceive('send')
            ->once()
            ->with(Mockery::on(fn (PushSubscription $record): bool => $record->is($subscription)), Mockery::type('array'))
            ->andReturn(['success' => true, 'expired' => false]);

        $job = new SendBrowserPushNotification($user->id, [
            'title' => 'Result submitted',
        ]);

        $job->handle($sender);

        $this->assertNotNull($subscription->fresh()->last_used_at);
    }

    public function test_expired_delivery_deletes_the_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = $user->pushSubscriptions()->create([
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
        ]);

        $sender = Mockery::mock(BrowserPushSender::class);
        $sender->shouldReceive('isConfigured')->once()->andReturn(true);
        $sender->shouldReceive('send')
            ->once()
            ->with(Mockery::on(fn (PushSubscription $record): bool => $record->is($subscription)), Mockery::type('array'))
            ->andReturn(['success' => false, 'expired' => true]);

        $job = new SendBrowserPushNotification($user->id, [
            'title' => 'Result submitted',
        ]);

        $job->handle($sender);

        $this->assertDatabaseMissing(PushSubscription::class, [
            'id' => $subscription->id,
        ]);
    }
}
