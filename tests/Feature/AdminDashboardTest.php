<?php

namespace Tests\Feature;

use App\Filament\Pages\NotificationSettings;
use App\Filament\Widgets\NotificationSettingsTable;
use App\Filament\Widgets\UserStatsOverview;
use App\Listeners\PreventDisabledNotification;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Notifications\InviteNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSending;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_updated_widgets(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSeeLivewire(UserStatsOverview::class);
        $response->assertDontSeeLivewire(NotificationSettingsTable::class);
        $response->assertSee('Notifications');
        $response->assertSee(NotificationSettings::getUrl());
        $response->assertSee('filament-copilot-button', false);
        $response->assertSee('filament-copilot-chat', false);
        $response->assertSee('dispatchLivewire(name, params = {}, bubbles = true)', false);
        $response->assertSee('await $wire.handleStreamComplete(', false);
    }

    public function test_notification_settings_have_their_own_admin_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(NotificationSettings::getUrl());

        $response->assertOk();
        $response->assertSeeLivewire(NotificationSettingsTable::class);
        $this->assertDatabaseCount('notification_settings', 10);
        $setting = NotificationSetting::query()->firstOrFail();

        Livewire::test(NotificationSettingsTable::class)
            ->assertCanSeeTableRecords(NotificationSetting::query()->get())
            ->assertSee('League night tonight')
            ->call('updateTableColumnState', 'enabled', (string) $setting->getKey(), false);

        $this->assertFalse($setting->refresh()->enabled);
    }

    public function test_disabled_notification_setting_stops_every_delivery_channel(): void
    {
        $setting = NotificationSetting::query()
            ->where('notification_type', InviteNotification::class)
            ->firstOrFail();

        $setting->update(['enabled' => false]);

        $event = new NotificationSending(
            User::factory()->create(),
            new InviteNotification('example-token'),
            'mail',
        );

        $this->assertFalse(app(PreventDisabledNotification::class)->handle($event));

        $setting->update(['enabled' => true]);

        $this->assertTrue(app(PreventDisabledNotification::class)->handle($event));
    }
}
