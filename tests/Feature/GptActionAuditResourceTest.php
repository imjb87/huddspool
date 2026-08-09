<?php

namespace Tests\Feature;

use App\Filament\Resources\GptActionAudits\GptActionAuditResource;
use App\Filament\Resources\GptActionAudits\Pages\ListGptActionAudits;
use App\Filament\Resources\GptActionAudits\Pages\ViewGptActionAudit;
use App\Models\GptActionAudit;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GptActionAuditResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_and_view_gpt_action_audits(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $team = Team::factory()->create();
        $audit = GptActionAudit::query()->create([
            'administrator_id' => $administrator->id,
            'action' => 'update_team_venue',
            'subject_type' => Team::class,
            'subject_id' => $team->id,
            'before' => ['venue_id' => 10],
            'after' => ['venue_id' => 20],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Custom GPT',
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($administrator)
            ->test(ListGptActionAudits::class)
            ->assertCanSeeTableRecords([$audit])
            ->assertSee('update_team_venue')
            ->assertSee($administrator->name);

        Livewire::actingAs($administrator)
            ->test(ViewGptActionAudit::class, ['record' => $audit->getRouteKey()])
            ->assertSee('update_team_venue')
            ->assertSee('Custom GPT');
    }

    public function test_audit_resource_is_read_only(): void
    {
        $audit = new GptActionAudit;

        $this->assertFalse(GptActionAuditResource::canCreate());
        $this->assertFalse(GptActionAuditResource::canEdit($audit));
        $this->assertFalse(GptActionAuditResource::canDelete($audit));
        $this->assertFalse(GptActionAuditResource::canDeleteAny());
        $this->assertArrayNotHasKey('create', GptActionAuditResource::getPages());
        $this->assertArrayNotHasKey('edit', GptActionAuditResource::getPages());
    }
}
