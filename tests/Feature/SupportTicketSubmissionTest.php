<?php

namespace Tests\Feature;

use App\Domain\Support\SupportTicketService;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use App\Notifications\SupportTicketAcknowledgedNotification;
use App\Notifications\SupportTicketReplyNotification;
use App\Notifications\SupportTicketSubmittedNotification;
use App\Notifications\SupportTicketUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SupportTicketSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_ticket_page_lists_previous_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = SupportTicket::factory()->create([
            'user_id' => $user->getKey(),
            'requester_name' => $user->name,
            'requester_email' => $user->email,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('support.tickets'));

        $response
            ->assertOk()
            ->assertSee('data-support-tickets-page', false)
            ->assertSee('data-account-header', false)
            ->assertSee('data-account-nav', false)
            ->assertSee('aria-label="Account navigation"', false)
            ->assertSee('data-state="active"', false)
            ->assertSee('href="'.route('account.show').'"', false)
            ->assertSee('href="'.route('support.tickets.create').'"', false)
            ->assertSeeText($ticket->ticket_number)
            ->assertSeeText('Submitted')
            ->assertSeeText('Last activity')
            ->assertSee('rounded-md border px-2.5 py-0.5', false)
            ->assertDontSeeText($ticket->title)
            ->assertDontSeeText('Priority:');
    }

    public function test_new_support_ticket_page_prefills_authenticated_user_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'support@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('support.tickets.create'));

        $response
            ->assertOk()
            ->assertSee('data-support-ticket-page', false)
            ->assertSee('data-account-header', false)
            ->assertSee('data-account-nav', false)
            ->assertSee('ui-result-button ui-result-button-primary', false)
            ->assertSee('value="Test User"', false)
            ->assertSee('value="support@example.com"', false);
    }

    public function test_legacy_support_ticket_route_redirects_to_account_support(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/support/tickets')
            ->assertRedirect('/account/support');
    }

    public function test_authenticated_user_can_submit_a_support_ticket_and_relevant_users_are_notified(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create([
            'receives_support_ticket_notifications' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('support.tickets.store'), [
                'name' => 'Test User',
                'email' => 'support@example.com',
                'message' => 'Need help with a score submission.',
            ]);

        $response
            ->assertRedirect(route('support.tickets'))
            ->assertSessionHas('success', 'Thanks! Your support ticket has been submitted.');

        $ticket = SupportTicket::query()->firstOrFail();
        $reply = SupportTicketReply::query()->firstOrFail();

        $this->assertSame($user->id, $ticket->user_id);
        $this->assertSame('Test User', $ticket->requester_name);
        $this->assertSame('support@example.com', $ticket->requester_email);
        $this->assertSame('Need help with a score submission.', $ticket->message);
        $this->assertSame(SupportTicketStatus::Open, $ticket->status);
        $this->assertSame($user->id, $reply->user_id);
        $this->assertSame($ticket->id, $reply->support_ticket_id);
        $this->assertSame('Need help with a score submission.', $reply->content);
        $this->assertFalse($reply->is_internal_note);

        Notification::assertSentTo($user, SupportTicketAcknowledgedNotification::class);
        Notification::assertSentTo($admin, SupportTicketSubmittedNotification::class);
    }

    public function test_support_ticket_submission_requires_name_email_and_message(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('support.tickets.create'))
            ->post(route('support.tickets.store'), []);

        $response
            ->assertRedirect(route('support.tickets.create'))
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('support_tickets', 0);
        $this->assertDatabaseCount('support_ticket_replies', 0);
    }

    public function test_honeypot_submission_returns_success_without_creating_ticket_data(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('support.tickets.store'), [
                'website' => 'https://spam.example.com',
            ]);

        $response
            ->assertRedirect(route('support.tickets'))
            ->assertSessionHas('success', 'Thanks! Your support ticket has been submitted.');

        $this->assertDatabaseCount('support_tickets', 0);
        $this->assertDatabaseCount('support_ticket_replies', 0);
    }

    public function test_user_can_view_and_reply_to_their_ticket(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $ticket = SupportTicket::factory()->create(['user_id' => $user->getKey()]);
        SupportTicketReply::factory()->create([
            'support_ticket_id' => $ticket->getKey(),
            'user_id' => $user->getKey(),
            'content' => 'Original support request',
        ]);
        $admin = User::factory()->create(['is_admin' => true]);
        SupportTicketReply::factory()->create([
            'support_ticket_id' => $ticket->getKey(),
            'user_id' => $admin->getKey(),
            'content' => 'Admin reply',
        ]);

        app(SupportTicketService::class)->update(
            $ticket,
            ['status' => SupportTicketStatus::InProgress],
            $admin,
        );

        $this->actingAs($user)
            ->get(route('support.tickets.show', $ticket))
            ->assertOk()
            ->assertSee('data-support-ticket-show', false)
            ->assertSeeText($ticket->ticket_number)
            ->assertSeeText('Original support request')
            ->assertSeeText('Admin reply')
            ->assertSee('justify-end', false)
            ->assertSee('rounded-br-md border-transparent bg-foreground text-background', false)
            ->assertSee('justify-start', false)
            ->assertSee('rounded-bl-md border-border bg-muted text-foreground', false)
            ->assertSeeText('Status updates')
            ->assertSeeText('In Progress')
            ->assertDontSeeText('Priority');

        $this->actingAs($user)
            ->post(route('support.tickets.reply', $ticket), [
                'content' => 'Here is some additional information.',
            ])
            ->assertRedirect(route('support.tickets.show', $ticket));

        $this->assertDatabaseHas('support_ticket_replies', [
            'support_ticket_id' => $ticket->getKey(),
            'user_id' => $user->getKey(),
            'content' => 'Here is some additional information.',
            'is_internal_note' => false,
        ]);
        $this->assertSame(SupportTicketStatus::Answered, $ticket->refresh()->status);
    }

    public function test_user_cannot_view_or_reply_to_another_users_ticket(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ticket = SupportTicket::factory()->create(['user_id' => $owner->getKey()]);

        $this->actingAs($otherUser)
            ->get(route('support.tickets.show', $ticket))
            ->assertNotFound();

        $this->actingAs($otherUser)
            ->post(route('support.tickets.reply', $ticket), ['content' => 'Not authorised'])
            ->assertForbidden();
    }

    public function test_ticket_replies_and_status_changes_notify_the_relevant_users(): void
    {
        Notification::fake();

        $requester = User::factory()->create();
        $admin = User::factory()->create([
            'receives_support_ticket_notifications' => true,
        ]);
        $ticket = SupportTicket::factory()->create(['user_id' => $requester->getKey()]);
        $service = app(SupportTicketService::class);

        $service->addReply($ticket, $admin, 'We are looking into this.');
        $service->update($ticket, ['status' => SupportTicketStatus::InProgress], $admin);
        $service->addReply($ticket->refresh(), $requester, 'Thanks for the update.');

        Notification::assertSentTo($requester, SupportTicketReplyNotification::class, function (SupportTicketReplyNotification $notification): bool {
            return $notification->fromAdministrator;
        });
        Notification::assertSentTo($requester, SupportTicketUpdatedNotification::class);
        Notification::assertSentTo($admin, SupportTicketReplyNotification::class, function (SupportTicketReplyNotification $notification): bool {
            return ! $notification->fromAdministrator;
        });
    }
}
