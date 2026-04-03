<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Jobs\SendBrowserPushNotification;
use App\Models\Fixture;
use App\Models\PushSubscription;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Notifications\LeagueNightTonightNotification;
use App\Support\Notifications\DatabaseNotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_store_push_subscription(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('account.push-subscriptions.store'), [
                'endpoint' => 'https://example.com/push/123',
                'public_key' => 'public-key',
                'auth_token' => 'auth-token',
                'content_encoding' => 'aes128gcm',
            ])
            ->assertOk()
            ->assertJson([
                'enabled' => true,
            ]);

        $this->assertDatabaseHas(PushSubscription::class, [
            'user_id' => $user->id,
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
        ]);
        $this->assertNotNull($user->fresh()->push_prompted_at);
    }

    public function test_authenticated_user_can_acknowledge_the_one_time_push_permission_prompt(): void
    {
        $user = User::factory()->create([
            'push_prompted_at' => null,
        ]);

        $this->actingAs($user)
            ->postJson(route('account.push-permission.acknowledge'))
            ->assertOk()
            ->assertJson([
                'acknowledged' => true,
            ]);

        $this->assertNotNull($user->fresh()->push_prompted_at);
    }

    public function test_authenticated_user_can_delete_push_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = PushSubscription::query()->create([
            'user_id' => $user->id,
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
        ]);

        $this->actingAs($user)
            ->deleteJson(route('account.push-subscriptions.destroy'), [
                'endpoint' => $subscription->endpoint,
            ])
            ->assertOk()
            ->assertJson([
                'enabled' => false,
            ]);

        $this->assertDatabaseMissing(PushSubscription::class, [
            'id' => $subscription->id,
        ]);
    }

    public function test_dispatcher_queues_browser_push_job_for_users_with_subscriptions(): void
    {
        Queue::fake();

        $season = Season::factory()->create(['is_open' => true]);
        $ruleset = Ruleset::factory()->create();
        $section = Section::factory()->create([
            'season_id' => $season->id,
            'ruleset_id' => $ruleset->id,
        ]);

        Team::factory()->create();

        $homeTeam = Team::factory()->create();
        $awayTeam = Team::factory()->create();

        $section->teams()->attach($homeTeam->id, ['sort' => 1]);
        $section->teams()->attach($awayTeam->id, ['sort' => 2]);

        $fixture = Fixture::factory()->create([
            'season_id' => $season->id,
            'section_id' => $section->id,
            'ruleset_id' => $ruleset->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'fixture_date' => now(),
        ]);

        $user = User::factory()->create([
            'team_id' => $homeTeam->id,
            'role' => UserRole::TeamAdmin->value,
        ]);

        $user->pushSubscriptions()->create([
            'endpoint' => 'https://example.com/push/123',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aes128gcm',
        ]);

        app(DatabaseNotificationDispatcher::class)->sendOnce(
            collect([$user]),
            new LeagueNightTonightNotification($fixture),
        );

        Queue::assertPushed(
            SendBrowserPushNotification::class,
            fn (SendBrowserPushNotification $job): bool => $job->userId === $user->id
                && data_get($job->payload, 'title') === 'League night is tonight'
        );
    }
}
