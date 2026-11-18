<?php

namespace Tests\Feature;

use App\Enums\SupportTicketStatus;
use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Models\SupportTicket;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class SupportTicketAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_view_and_update_application_owned_support_tickets(): void
    {
        $this->assertFalse(Schema::hasColumn('support_tickets', 'assignee_id'));
        $this->assertFalse(Schema::hasColumn('support_tickets', 'priority'));

        $admin = User::factory()->create(['is_admin' => true]);
        $requester = User::factory()->create();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $requester->getKey(),
            'requester_name' => $requester->name,
            'requester_email' => $requester->email,
        ]);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(ListSupportTickets::class)
            ->assertCanSeeTableRecords([$ticket])
            ->assertTableColumnExists('ticket_number')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('last_activity_at')
            ->assertTableColumnExists('status')
            ->assertTableColumnDoesNotExist('title')
            ->assertTableColumnDoesNotExist('requester_name');

        Livewire::actingAs($admin)
            ->test(ViewSupportTicket::class, [
                'record' => $ticket->getRouteKey(),
            ])
            ->assertSee($ticket->ticket_number);

        Livewire::actingAs($admin)
            ->test(EditSupportTicket::class, [
                'record' => $ticket->getRouteKey(),
            ])
            ->fillForm([
                'status' => SupportTicketStatus::InProgress->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $ticket->refresh();

        $this->assertSame(SupportTicketStatus::InProgress, $ticket->status);
        $this->assertDatabaseHas('support_ticket_activities', [
            'support_ticket_id' => $ticket->getKey(),
            'description' => 'Status changed',
            'old_value' => SupportTicketStatus::Open->value,
            'new_value' => SupportTicketStatus::InProgress->value,
        ]);
    }
}
