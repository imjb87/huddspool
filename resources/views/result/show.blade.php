@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-result-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Result</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $result->fixture->section->name }}</p>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    <section class="py-1" data-result-info-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result information</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Match details, venue, and links back to the wider section schedule.
                                </p>
                            </div>

                            <div class="space-y-5 lg:col-span-2">
                                @if (! $result->is_confirmed)
                                    @can('resumeSubmission', $result)
                                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-900/60 dark:bg-red-950/40">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-red-700 dark:text-red-300">Result submission</p>
                                                <p class="text-sm text-red-900 dark:text-red-100">
                                                    {{ $result->home_team_name }} vs {{ $result->away_team_name }}
                                                </p>
                                            </div>

                                            <a href="{{ route('result.create', $result->fixture_id) }}"
                                                class="inline-flex items-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                                Continue submitting result
                                            </a>
                                        </div>
                                    @endcan
                                @endif

                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</p>
                                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $result->home_team_name }}
                                            <span class="font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                            {{ $result->away_team_name }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $result->fixture->fixture_date->format('l jS F Y') }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ruleset</p>
                                        <a href="{{ route('ruleset.section.show', ['ruleset' => $result->fixture->section->ruleset, 'section' => $result->fixture->section, 'tab' => 'fixtures-results']) }}"
                                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                            {{ $result->fixture->section->ruleset->name }}
                                        </a>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</p>
                                        @if ($result->fixture->venue)
                                            <a href="{{ route('venue.show', $result->fixture->venue) }}"
                                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                {{ $result->fixture->venue->name }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-result-card-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Frame-by-frame scores, match totals, and submission details.
                                </p>
                            </div>

                            <div class="space-y-5 lg:col-span-2">
                                @if (! $result->is_confirmed)
                                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 dark:border-yellow-900/60 dark:bg-yellow-950/30">
                                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                            This result is still in progress and will remain editable until it is locked.
                                        </p>
                                    </div>
                                @endif

                                @if ($result->is_overridden)
                                    <div class="px-4 py-10 text-center sm:px-6">
                                        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result overridden</h3>
                                            <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                                This match result was overridden by an admin.
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-4" data-result-card-shell>
                                        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80" data-result-card-frames>
                                            @foreach ($result->frames as $key => $frame)
                                                <div class="py-4" wire:key="result-frame-{{ $frame->id }}">
                                                    <div class="space-y-3" data-result-card-band>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Frame {{ $key + 1 }}
                                                        </p>

                                                        <div class="flex items-center gap-3">
                                                            <div class="min-w-0 flex-1">
                                                                @if ($frame->home_player_id)
                                                                    <a href="{{ route('player.show', $frame->homePlayer) }}"
                                                                        class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 transition hover:text-gray-500 dark:text-gray-100 dark:hover:text-gray-300">
                                                                        <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                                            src="{{ $frame->homePlayer->avatar_url }}"
                                                                            alt="{{ $frame->homePlayer->name }} avatar">
                                                                        <span class="truncate">{{ $frame->homePlayer->name }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                        <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                                            src="{{ asset('/images/user.jpg') }}"
                                                                            alt="Awarded">
                                                                        <span class="truncate">Awarded</span>
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <div class="shrink-0">
                                                                <div class="inline-flex h-7 w-9 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-800 dark:text-gray-200 dark:ring-zinc-700"
                                                                    data-result-frame-score-pill>
                                                                    {{ $frame->home_score }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-3">
                                                            <div class="min-w-0 flex-1">
                                                                @if ($frame->away_player_id)
                                                                    <a href="{{ route('player.show', $frame->awayPlayer) }}"
                                                                        class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 transition hover:text-gray-500 dark:text-gray-100 dark:hover:text-gray-300">
                                                                        <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                                            src="{{ $frame->awayPlayer->avatar_url }}"
                                                                            alt="{{ $frame->awayPlayer->name }} avatar">
                                                                        <span class="truncate">{{ $frame->awayPlayer->name }}</span>
                                                                    </a>
                                                                @else
                                                                    <span class="inline-flex min-w-0 items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                        <img class="h-6 w-6 shrink-0 rounded-full object-cover"
                                                                            src="{{ asset('/images/user.jpg') }}"
                                                                            alt="Awarded">
                                                                        <span class="truncate">Awarded</span>
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <div class="shrink-0">
                                                                <div class="inline-flex h-7 w-9 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-center text-xs font-extrabold text-gray-700 ring-1 ring-gray-200 dark:bg-zinc-800 dark:text-gray-200 dark:ring-zinc-700"
                                                                    data-result-frame-score-pill>
                                                                    {{ $frame->away_score }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="flex items-start justify-between gap-4 py-1" data-result-card-band>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match total</p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $result->home_team_name }}
                                                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                                                    {{ $result->away_team_name }}
                                                </p>
                                            </div>

                                            <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                                <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
                                                    data-result-score-pill>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $result->home_score }}</div>
                                                    <div class="w-px bg-white/25"></div>
                                                    <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $result->away_score }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($result->is_confirmed && $result->submittedBy)
                                    @php
                                        $submittedAt = $result->submitted_at ?? $result->created_at;
                                    @endphp
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Submitted by {{ $result->submittedBy->name }} on {{ $submittedAt->format('j M Y') }} at {{ $submittedAt->format('H:i') }}.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <x-logo-clouds  />
    </div>
@endsection
