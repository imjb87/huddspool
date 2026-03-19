@php($title = 'Support tickets')

@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-support-ticket-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Support</h1>
                </div>
            </div>

            <div class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75" data-account-nav>
                <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
                    <nav class="-ml-3 flex gap-2">
                        <a href="{{ route('account.show') }}"
                            class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-800/70 dark:hover:text-gray-100">
                            Profile
                        </a>
                        @if (auth()->user()?->isCaptain() || auth()->user()?->isTeamAdmin())
                            <a href="{{ route('account.team') }}"
                                class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-800/70 dark:hover:text-gray-100">
                                Team
                            </a>
                        @endif
                        <a href="{{ route('support.tickets') }}"
                            class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 transition dark:bg-zinc-700 dark:text-gray-300">
                            Support
                        </a>
                    </nav>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                @if (session('success'))
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/80 dark:bg-green-950/60 dark:text-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <section class="py-1">
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Support request</h3>
                            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Send a message to the admin team and we will get back to you as soon as we can.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <form method="POST" action="{{ route('support.tickets.store') }}" class="space-y-5">
                                @csrf

                                <div class="hidden">
                                    <label for="support-website" class="sr-only">Website</label>
                                    <input id="support-website" name="website" type="text" autocomplete="off" tabindex="-1">
                                </div>

                                <div>
                                    <label for="support-name" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Name</label>
                                    <input id="support-name"
                                        name="name"
                                        type="text"
                                        autocomplete="name"
                                        value="{{ $name }}"
                                        class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="support-email" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Email address</label>
                                    <input id="support-email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        value="{{ $email }}"
                                        class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="support-message" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Message</label>
                                    <textarea id="support-message"
                                        name="message"
                                        rows="7"
                                        class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 dark:focus:border-green-500 dark:focus:ring-green-500/20">{{ $supportMessage }}</textarea>
                                    @error('message')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="pt-1">
                                    <button type="submit"
                                        class="inline-flex min-w-24 items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                        Send support request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
