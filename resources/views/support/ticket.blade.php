@extends('layouts.app')

@section('title', 'Support ticket '.$ticket->ticket_number)

@section('content')
    <x-account.support-layout data-support-ticket-show>
        <div class="space-y-6">
            @if (session('success'))
                <div class="rounded-lg border border-green-500/50 bg-green-50/50 px-4 py-3.5 text-sm text-green-900 dark:border-green-500/50 dark:bg-green-950/20 dark:text-green-200" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('support.tickets') }}" class="ui-link">Back to support requests</a>
            </div>

            <section class="ui-section" data-support-ticket-summary>
                <div class="ui-shell-grid">
                    <div class="ui-section-intro gap-2">
                        <div class="ui-section-intro-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.573-.598-3.75A11.959 11.959 0 0 1 12 2.714Z" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                            <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Support ticket</h2>
                            <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">Review the ticket status, submission date, and latest activity.</p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <x-ui-card>
                            <div class="ui-card-body space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->ticket_number }}</p>
                                        <div class="mt-1 flex flex-col gap-1 text-xs text-gray-500 dark:text-gray-400 sm:flex-row sm:gap-4">
                                            <span><span class="font-medium text-gray-700 dark:text-gray-300">Submitted</span> {{ $ticket->created_at?->format('j F Y, H:i') ?? '—' }}</span>
                                            <span><span class="font-medium text-gray-700 dark:text-gray-300">Last activity</span> {{ $ticket->last_activity_at?->format('j F Y, H:i') ?? '—' }}</span>
                                        </div>
                                    </div>

                                    <x-ui-badge :variant="$ticket->status?->badgeVariant() ?? 'secondary'">
                                        {{ $ticket->status?->label() ?? 'Unknown' }}
                                    </x-ui-badge>
                                </div>
                            </div>
                        </x-ui-card>
                    </div>
                </div>
            </section>

            <section class="ui-section" data-support-ticket-conversation>
                <div class="ui-shell-grid">
                    <div class="ui-section-intro gap-2">
                        <div class="ui-section-intro-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm0 0H8.25m4.125 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm0 0h-.375m4.125 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 21.97a5.969 5.969 0 0 1-.474-2.845A8.966 8.966 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                            <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Conversation</h2>
                            <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">Read the request and any replies from you or the admin team.</p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="grid gap-4">
                            @foreach ($ticket->replies as $reply)
                                @php($isUserReply = $reply->user_id === $ticket->user_id)

                                <div class="flex {{ $isUserReply ? 'justify-end' : 'justify-start' }}">
                                    <article class="max-w-[90%] rounded-2xl border px-4 py-3 shadow-xs sm:max-w-2xl {{ $isUserReply ? 'rounded-br-md border-transparent bg-foreground text-background' : 'rounded-bl-md border-border bg-muted text-foreground' }}" data-support-ticket-reply>
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between gap-3 text-xs {{ $isUserReply ? 'text-background/70' : 'text-muted-foreground' }}">
                                                <span class="font-medium">{{ $isUserReply ? 'You' : ($reply->user?->name ?? 'Huddspool admin') }}</span>
                                                <time datetime="{{ $reply->created_at?->toIso8601String() }}">{{ $reply->created_at?->format('j F Y, H:i') }}</time>
                                            </div>
                                            <p class="whitespace-pre-line text-sm leading-6">{{ $reply->content }}</p>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            @if ($ticket->activities->isNotEmpty())
                <section class="ui-section" data-support-ticket-status-updates>
                    <div class="ui-shell-grid">
                        <div class="ui-section-intro gap-2">
                            <div class="ui-section-intro-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 5.25h15a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-15a1.5 1.5 0 0 1-1.5-1.5V6.75a1.5 1.5 0 0 1 1.5-1.5Z" />
                                </svg>
                            </div>
                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Status updates</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">See when the ticket workflow or conversation changed.</p>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="grid gap-3">
                                @foreach ($ticket->activities as $activity)
                                    <x-ui-card as="article" class="border-border bg-muted/30 px-4 py-3 text-sm" data-support-ticket-status-update>
                                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                            <p class="font-medium text-foreground">{{ $activity->description }}</p>
                                            <time class="text-xs text-muted-foreground" datetime="{{ $activity->created_at?->toIso8601String() }}">{{ $activity->created_at?->format('j F Y, H:i') }}</time>
                                        </div>
                                        <p class="mt-1 text-muted-foreground">
                                            {{ \Illuminate\Support\Str::headline($activity->old_value ?? 'Not set') }}
                                            <span aria-hidden="true">→</span>
                                            {{ \Illuminate\Support\Str::headline($activity->new_value ?? 'Not set') }}
                                        </p>
                                    </x-ui-card>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if ($ticket->isClosing())
                <x-ui-card class="border-border bg-muted/30 px-4 py-3.5 text-sm text-muted-foreground">This ticket is closed. Please create a new support request if you need further help.</x-ui-card>
            @else
                <section class="ui-section" data-support-ticket-reply-form>
                    <div class="ui-shell-grid">
                        <div class="ui-section-intro gap-2">
                            <div class="ui-section-intro-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Add a reply</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">Send more information to the admin team without opening a new ticket.</p>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <x-ui-card>
                                <form method="POST" action="{{ route('support.tickets.reply', $ticket) }}" class="ui-card-body space-y-4">
                                    @csrf
                                    <div>
                                        <label for="support-reply-content" class="block text-sm font-medium text-foreground">Message</label>
                                        <textarea id="support-reply-content" name="content" rows="6" class="mt-2 block min-h-32 w-full resize-y rounded-md border border-border bg-background px-3 py-2 text-sm leading-6 text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50">{{ old('content') }}</textarea>
                                        @error('content')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="flex justify-end">
                                        <x-ui-button type="submit">Send reply</x-ui-button>
                                    </div>
                                </form>
                            </x-ui-card>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </x-account.support-layout>
@endsection
