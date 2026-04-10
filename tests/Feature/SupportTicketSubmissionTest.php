<?php

namespace Tests\Feature;

use App\Models\User;
use daacreators\CreatorsTicketing\Database\Seeders\TicketStatusSeeder;
use daacreators\CreatorsTicketing\Models\Department;
use daacreators\CreatorsTicketing\Models\Form;
use daacreators\CreatorsTicketing\Models\FormField;
use daacreators\CreatorsTicketing\Models\Ticket;
use daacreators\CreatorsTicketing\Models\TicketReply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_ticket_page_prefills_authenticated_user_details_and_provisions_form(): void
    {
        $this->seed(TicketStatusSeeder::class);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'support@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('support.tickets'));

        $response
            ->assertOk()
            ->assertSee('data-support-ticket-page', false)
            ->assertSee('href="'.route('account.show').'"', false)
            ->assertSee('href="'.route('support.tickets').'"', false)
            ->assertSeeText('Support tickets |')
            ->assertSee('value="Test User"', false)
            ->assertSee('value="support@example.com"', false);

        $this->assertSame(1, Department::query()->count());
        $this->assertSame(1, Form::query()->count());
        $this->assertSame(3, FormField::query()->count());
    }

    public function test_legacy_support_ticket_route_redirects_to_account_support(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/support/tickets')
            ->assertRedirect('/account/support');
    }

    public function test_authenticated_user_can_submit_a_support_ticket(): void
    {
        $this->seed(TicketStatusSeeder::class);

        $user = User::factory()->create();

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

        $this->assertSame(1, Department::query()->count());
        $this->assertSame(1, Form::query()->count());
        $this->assertSame(3, FormField::query()->count());
        $this->assertSame(1, Ticket::query()->count());
        $this->assertSame(1, TicketReply::query()->count());

        $ticket = Ticket::query()->firstOrFail();
        $reply = TicketReply::query()->firstOrFail();

        $this->assertSame($user->id, $ticket->user_id);
        $this->assertSame([
            'name' => 'Test User',
            'email' => 'support@example.com',
            'message' => 'Need help with a score submission.',
        ], $ticket->custom_fields);
        $this->assertSame($user->id, $reply->user_id);
        $this->assertSame($ticket->id, $reply->ticket_id);
        $this->assertSame('Need help with a score submission.', $reply->content);
        $this->assertFalse($reply->is_internal_note);
    }

    public function test_support_ticket_submission_requires_name_email_and_message(): void
    {
        $this->seed(TicketStatusSeeder::class);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('support.tickets'))
            ->post(route('support.tickets.store'), []);

        $response
            ->assertRedirect(route('support.tickets'))
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertSame(0, Ticket::query()->count());
        $this->assertSame(0, TicketReply::query()->count());
    }

    public function test_honeypot_submission_returns_success_without_creating_ticket_data(): void
    {
        $this->seed(TicketStatusSeeder::class);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('support.tickets.store'), [
                'website' => 'https://spam.example.com',
            ]);

        $response
            ->assertRedirect(route('support.tickets'))
            ->assertSessionHas('success', 'Thanks! Your support ticket has been submitted.');

        $this->assertSame(0, Department::query()->count());
        $this->assertSame(0, Form::query()->count());
        $this->assertSame(0, FormField::query()->count());
        $this->assertSame(0, Ticket::query()->count());
        $this->assertSame(0, TicketReply::query()->count());
    }
}
