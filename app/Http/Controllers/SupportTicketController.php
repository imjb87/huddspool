<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportTicketRequest;
use App\Services\SupportTicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportTicketController extends Controller
{
    public function __construct(
        protected SupportTicketService $supportTickets,
    ) {}

    public function create(Request $request): View
    {
        $user = $request->user();

        $this->supportTickets->ensureFormExists();

        return view('support.tickets', [
            'name' => old('name', $user?->name ?? ''),
            'email' => old('email', $user?->email ?? ''),
            'supportMessage' => old('message', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->filled('website')) {
            return redirect()
                ->route('support.tickets')
                ->with('success', 'Thanks! Your support ticket has been submitted.');
        }

        $supportTicketRequest = new StoreSupportTicketRequest;

        $data = Validator::make(
            $request->all(),
            $supportTicketRequest->rules(),
            $supportTicketRequest->messages(),
            $supportTicketRequest->attributes(),
        )->validate();

        $requester = $user;

        $this->supportTickets->submit($requester, $data);

        return redirect()
            ->route('support.tickets')
            ->with('success', 'Thanks! Your support ticket has been submitted.');
    }
}
