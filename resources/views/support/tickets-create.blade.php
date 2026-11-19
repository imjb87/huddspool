@extends('layouts.app')

@section('title', 'New support request')

@section('content')
    <x-account.support-layout data-support-ticket-page>
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('support.tickets') }}" class="ui-link">Back to support requests</a>
            </div>

                <section class="ui-section">
                    <div class="ui-shell-grid">
                        <div class="ui-section-intro gap-2">
                            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lifebuoy size-5 text-neutral-700 dark:text-neutral-200">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    <path d="M12 3l0 6" />
                                    <path d="M12 15l0 6" />
                                    <path d="M3 12l6 0" />
                                    <path d="M15 12l6 0" />
                                </svg>
                            </span>
                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Support request</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">Tell the admin team what has happened and include enough detail for them to act.</p>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <x-ui-card>
                                <form method="POST" action="{{ route('support.tickets.store') }}" class="ui-card-body space-y-5">
                                    @csrf

                                    <div class="hidden">
                                        <label for="support-website" class="sr-only">Website</label>
                                        <input id="support-website" name="website" type="text" autocomplete="off" tabindex="-1">
                                    </div>

                                    <div class="grid gap-5 sm:grid-cols-2">
                                        <div>
                                            <label for="support-name" class="block text-sm font-medium text-foreground">Name</label>
                                            <input id="support-name" name="name" type="text" autocomplete="name" value="{{ $name }}" class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50">
                                            @error('name')
                                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="support-email" class="block text-sm font-medium text-foreground">Email address</label>
                                            <input id="support-email" name="email" type="email" autocomplete="email" value="{{ $email }}" class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50">
                                            @error('email')
                                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="support-message" class="block text-sm font-medium text-foreground">Message</label>
                                        <textarea id="support-message" name="message" rows="7" class="mt-2 block min-h-36 w-full resize-y rounded-md border border-border bg-background px-3 py-2 text-sm leading-6 text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50">{{ $supportMessage }}</textarea>
                                        @error('message')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex justify-end">
                                        <x-ui-button type="submit" class="min-w-24">Send support request</x-ui-button>
                                    </div>
                                </form>
                            </x-ui-card>
                        </div>
                    </div>
                </section>
        </div>
    </x-account.support-layout>
@endsection
