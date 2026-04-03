<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationRetentionCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_purge_old_notifications_command_only_deletes_notifications_older_than_two_weeks(): void
    {
        Carbon::setTestNow('2026-04-03 12:00:00');

        $oldNotification = DatabaseNotification::query()->create([
            'id' => (string) Str::uuid(),
            'type' => 'test.notification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => json_encode([
                'title' => 'Old notification',
                'body' => 'This one should be purged.',
                'action_url' => route('home'),
            ], JSON_THROW_ON_ERROR),
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(15),
        ]);

        $thresholdNotification = DatabaseNotification::query()->create([
            'id' => (string) Str::uuid(),
            'type' => 'test.notification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => json_encode([
                'title' => 'Threshold notification',
                'body' => 'This one should stay.',
                'action_url' => route('home'),
            ], JSON_THROW_ON_ERROR),
            'created_at' => now()->subWeeks(2),
            'updated_at' => now()->subWeeks(2),
        ]);

        $recentNotification = DatabaseNotification::query()->create([
            'id' => (string) Str::uuid(),
            'type' => 'test.notification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => json_encode([
                'title' => 'Recent notification',
                'body' => 'This one should stay.',
                'action_url' => route('home'),
            ], JSON_THROW_ON_ERROR),
            'created_at' => now()->subDays(13),
            'updated_at' => now()->subDays(13),
        ]);

        $this->artisan('app:purge-old-notifications')
            ->expectsOutput('Purged 1 old notifications.')
            ->assertSuccessful();

        $this->assertDatabaseMissing('notifications', ['id' => $oldNotification->id]);
        $this->assertDatabaseHas('notifications', ['id' => $thresholdNotification->id]);
        $this->assertDatabaseHas('notifications', ['id' => $recentNotification->id]);
    }
}
