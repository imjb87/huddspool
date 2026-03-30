@extends('layouts.app')

@section('title', 'Support tickets')

@section('content')
    <div class="ui-page-shell" data-support-ticket-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Account</p>
                        <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Support</h1>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75" data-account-nav>
            <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
                <nav class="flex gap-2">
                    <a href="{{ route('account.show') }}" class="ui-button-secondary shrink-0">
                        Profile
                    </a>
                    @if (auth()->user()?->can(\App\Enums\PermissionName::SubmitLeagueResults->value))
                        <a href="{{ route('account.team') }}" class="ui-button-secondary shrink-0">
                            Team
                        </a>
                    @endif
                    <a href="{{ route('support.tickets') }}" class="ui-button-primary shrink-0">
                        Support
                    </a>
                </nav>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                @if (session('success'))
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/80 dark:bg-green-950/60 dark:text-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <section class="ui-section">
                    <div class="ui-shell-grid">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Support request</h3>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Send a message to the admin team and we will get back to you as soon as we can.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="ui-card">
                                <form method="POST" action="{{ route('support.tickets.store') }}" class="ui-card-body space-y-5">
                                    @csrf

                                    <div class="hidden">
                                        <label for="support-website" class="sr-only">Website</label>
                                        <input id="support-website" name="website" type="text" autocomplete="off" tabindex="-1">
                                    </div>

                                    <div class="grid gap-5 sm:grid-cols-2">
                                        <div>
                                            <label for="support-name" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Name</label>
                                            <input id="support-name"
                                                name="name"
                                                type="text"
                                                autocomplete="name"
                                                value="{{ $name }}"
                                                class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20">
                                            @error('name')
                                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="support-email" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Email address</label>
                                            <input id="support-email"
                                                name="email"
                                                type="email"
                                                autocomplete="email"
                                                value="{{ $email }}"
                                                class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20">
                                            @error('email')
                                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="support-message" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Message</label>
                                        <textarea id="support-message"
                                            name="message"
                                            rows="7"
                                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20">{{ $supportMessage }}</textarea>
                                        @error('message')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="ui-button-primary min-w-24">
                                            Send support request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
