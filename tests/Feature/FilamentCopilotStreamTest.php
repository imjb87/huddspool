<?php

namespace Tests\Feature;

use App\Http\Controllers\Filament\CopilotStreamController;
use App\Models\User;
use App\Support\Copilot\Tools\ListPagesTool;
use App\Support\Copilot\Tools\ListResourcesTool;
use App\Support\Copilot\Tools\ListWidgetsTool;
use App\Support\Copilot\Tools\RecallTool;
use App\Support\Copilot\Tools\RunToolTool;
use EslamRedaDiv\FilamentCopilot\Http\Controllers\StreamController;
use EslamRedaDiv\FilamentCopilot\Tools\ListPagesTool as PackageListPagesTool;
use EslamRedaDiv\FilamentCopilot\Tools\ListResourcesTool as PackageListResourcesTool;
use EslamRedaDiv\FilamentCopilot\Tools\ListWidgetsTool as PackageListWidgetsTool;
use EslamRedaDiv\FilamentCopilot\Tools\RecallTool as PackageRecallTool;
use EslamRedaDiv\FilamentCopilot\Tools\RunToolTool as PackageRunToolTool;
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

    public function test_empty_schema_discovery_tools_are_overridden_by_the_application(): void
    {
        $this->assertInstanceOf(ListResourcesTool::class, app(PackageListResourcesTool::class));
        $this->assertInstanceOf(ListPagesTool::class, app(PackageListPagesTool::class));
        $this->assertInstanceOf(ListWidgetsTool::class, app(PackageListWidgetsTool::class));
        $this->assertInstanceOf(RecallTool::class, app(PackageRecallTool::class));
        $this->assertInstanceOf(RunToolTool::class, app(PackageRunToolTool::class));
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
