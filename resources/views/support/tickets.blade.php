@extends('layouts.app')

@section('title', 'Support tickets')

@section('content')
    <x-account.support-layout data-support-tickets-page>
        <div class="space-y-6">
            @if (session('success'))
                <div class="rounded-lg border border-green-500/50 bg-green-50/50 px-4 py-3.5 text-sm text-green-900 dark:border-green-500/50 dark:bg-green-950/20 dark:text-green-200" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <section class="ui-section">
                <div class="ui-shell-grid">
                    <div class="ui-section-intro gap-2">
                        <div class="ui-section-intro-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.573-.598-3.75A11.959 11.959 0 0 1 12 2.714Z" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                            <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Support requests</h2>
                            <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">Review your previous requests or start a new conversation with the admin team.</p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="mb-4 flex justify-end">
                            <a href="{{ route('support.tickets.create') }}" class="ui-result-button ui-result-button-primary shrink-0">New support request</a>
                        </div>

                        @if ($tickets->isEmpty())
                            <div class="ui-card">
                                <div class="ui-card-body text-sm text-muted-foreground">You have not submitted a support request yet.</div>
                            </div>
                        @else
                            <div class="ui-card">
                                <div class="ui-card-rows">
                                    @foreach ($tickets as $ticket)
                                        <a href="{{ route('support.tickets.show', $ticket) }}" class="ui-card-row-link" data-support-ticket-card>
                                            <div class="ui-card-row items-start px-4 sm:items-center sm:px-5">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->ticket_number }}</p>
                                                    <div class="mt-1 flex flex-col gap-1 text-xs text-gray-500 dark:text-gray-400 sm:flex-row sm:gap-4">
                                                        <span><span class="font-medium text-gray-700 dark:text-gray-300">Submitted</span> {{ $ticket->created_at?->format('j F Y, H:i') ?? '—' }}</span>
                                                        <span><span class="font-medium text-gray-700 dark:text-gray-300">Last activity</span> {{ $ticket->last_activity_at?->format('j F Y, H:i') ?? '—' }}</span>
                                                    </div>
                                                </div>

                                                <x-ui-badge :variant="$ticket->status?->badgeVariant() ?? 'secondary'" class="shrink-0">
                                                    {{ $ticket->status?->label() ?? 'Unknown' }}
                                                </x-ui-badge>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </x-account.support-layout>
@endsection
