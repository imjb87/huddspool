<?php

namespace App\Http\Controllers;

use App\Domain\Support\SupportTicketService;
use App\Http\Requests\StoreSupportTicketReplyRequest;
use App\Http\Requests\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function __construct(
        protected SupportTicketService $supportTickets,
    ) {}

    public function index(Request $request): View
    {
        $user = $this->user($request);

        return view('support.tickets', [
            'tickets' => SupportTicket::query()
                ->where('user_id', $user->getKey())
                ->with('replies')
                ->latest('last_activity_at')
                ->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $user = $this->user($request);

        return view('support.tickets-create', [
            'name' => old('name', $user?->name ?? ''),
            'email' => old('email', $user?->email ?? ''),
            'supportMessage' => old('message', ''),
        ]);
    }

    public function store(StoreSupportTicketRequest $request): RedirectResponse
    {
        if ($request->filled('website')) {
            return redirect()
                ->route('support.tickets')
                ->with('success', 'Thanks! Your support ticket has been submitted.');
        }

        $this->supportTickets->submit($this->user($request), $request->validated());

        return redirect()
            ->route('support.tickets')
            ->with('success', 'Thanks! Your support ticket has been submitted.');
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        $ticket = $this->ownedTicket($request, $ticket);

        return view('support.ticket', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(StoreSupportTicketReplyRequest $request, SupportTicket $ticket): RedirectResponse
    {
        $ticket = $this->ownedTicket($request, $ticket);

        $this->supportTickets->addReply(
            $ticket,
            $this->user($request),
            $request->validated()['content'],
        );

        return redirect()
            ->route('support.tickets.show', $ticket)
            ->with('success', 'Your reply has been added.');
    }

    private function user(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function ownedTicket(Request $request, SupportTicket $ticket): SupportTicket
    {
        abort_unless($ticket->user_id === $this->user($request)->getKey(), 404);

        return $ticket->load([
            'replies' => fn ($query) => $query
                ->where('is_internal_note', false)
                ->with('user')
                ->oldest(),
            'activities' => fn ($query) => $query
                ->where('description', 'Status changed')
                ->with('user')
                ->oldest(),
            'requester',
        ]);
    }
}
