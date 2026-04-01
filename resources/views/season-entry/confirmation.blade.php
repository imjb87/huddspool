@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] pb-10 dark:bg-neutral-950">
        <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
            <div class="space-y-8">
                <section class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-green-700 dark:text-green-400">Registration confirmed</p>
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $season->name }}</h1>
                        <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Your registration has been recorded. Use the reference below for your bank transfer. A copy of the invoice has been sent to {{ $entry->contact_email }}.
                        </p>
                    </div>

                    <div class="space-y-8 lg:col-span-2">
                        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-4 dark:border-green-900/60 dark:bg-green-950/40">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Reference number</p>
                            <p class="mt-1 text-2xl font-semibold tracking-wide text-green-900 dark:text-green-100">{{ $entry->reference }}</p>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('season.entry.invoice', ['season' => $season, 'entry' => $entry->reference]) }}"
                               target="_blank"
                               rel="noopener"
                               class="inline-flex items-center rounded-full bg-green-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-500">
                                Print invoice
                            </a>
                        </div>

                        <section>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Contact and venue</h2>
                            <div class="mt-3 divide-y divide-gray-200 dark:divide-neutral-800/80">
                                <div class="flex items-center justify-between gap-4 py-3 first:pt-0">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Contact</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $entry->contact_name }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-4 py-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $entry->contact_email }}</span>
                                </div>
                                @if (filled($entry->contact_telephone))
                                    <div class="flex items-center justify-between gap-4 py-3">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Telephone</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $entry->contact_telephone }}</span>
                                    </div>
                                @endif
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Venue</span>
                                    <div class="text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <div>{{ $entry->venue_name }}</div>
                                        @if (filled($entry->venue_address))
                                            <div class="mt-1 text-gray-500 dark:text-gray-400">{{ $entry->venue_address }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Teams</h2>
                            <div class="mt-3 divide-y divide-gray-200 dark:divide-neutral-800/80">
                                @forelse ($entry->teams as $team)
                                    <div class="flex items-start justify-between gap-4 py-4 first:pt-0">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $team->team_name }}</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $team->contact_name }}
                                                @if (filled($team->contact_telephone))
                                                    <span class="text-gray-400 dark:text-gray-500">·</span>
                                                    {{ $team->contact_telephone }}
                                                @endif
                                            </p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Team registration
                                                <span class="text-gray-400 dark:text-gray-500">·</span>
                                                {{ $team->ruleset?->name }}
                                                @if ($team->secondRuleset)
                                                    <span class="text-gray-400 dark:text-gray-500">/</span>
                                                    {{ $team->secondRuleset->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">£{{ number_format((float) $team->price, 2) }}</span>
                                    </div>
                                @empty
                                    <p class="py-3 text-sm text-gray-500 dark:text-gray-400">No teams added.</p>
                                @endforelse
                            </div>
                        </section>

                        <section>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Knockouts</h2>
                            <div class="mt-3 divide-y divide-gray-200 dark:divide-neutral-800/80">
                                @forelse ($entry->knockoutRegistrations as $knockoutEntry)
                                    <div class="flex items-start justify-between gap-4 py-4">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $knockoutEntry->knockout->name }}</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $knockoutEntry->entrant_name }}</p>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">£{{ number_format((float) $knockoutEntry->price, 2) }}</span>
                                    </div>
                                @empty
                                    <p class="py-3 text-sm text-gray-500 dark:text-gray-400">No knockouts added.</p>
                                @endforelse
                            </div>
                        </section>

                        <section>
                            <div class="mt-3 flex justify-end">
                                <div class="flex items-center gap-4 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Total</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">£{{ number_format((float) $entry->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
