<?php

namespace Tests\Feature;

use App\Http\Controllers\Filament\CopilotStreamController;
use App\Models\User;
use EslamRedaDiv\FilamentCopilot\Http\Controllers\StreamController;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentCopilotStreamTest extends TestCase
{
    use RefreshDatabase;

    public function test_copilot_stream_controller_is_overridden_by_the_application(): void
    {
        $this->assertInstanceOf(CopilotStreamController::class, app(StreamController::class));
    }

    public function test_admin_can_open_copilot_stream_without_backend_error(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        Filament::setCurrentPanel('admin');

        $response = $this->actingAs($admin)->post(route('filament-copilot.stream'), [
            'message' => 'Say hello in one short sentence.',
            'panel_id' => 'admin',
        ]);

        $response->assertOk();
        $response->assertStreamed();

        $streamed = $response->streamedContent();

        $this->assertStringNotContainsString('event: error', $streamed);
    }
}
