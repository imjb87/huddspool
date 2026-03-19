@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-950">
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
                        <div class="space-y-5">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
                                <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Frame-by-frame scores, match totals, and submission details.
                                </p>
                            </div>

                            <div class="space-y-5">
                                @if (! $result->is_confirmed)
                                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 dark:border-yellow-900/60 dark:bg-yellow-950/30">
                                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                            This result is still in progress and will remain editable until it is locked.
                                        </p>
                                    </div>
                                @endif

                                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800/80 dark:bg-zinc-900/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
                                    <div class="hidden bg-linear-to-br from-green-900 via-green-800 to-green-700 sm:flex">
                                        <div class="flex-1 px-4 py-2 text-right text-sm leading-6 font-semibold text-white">
                                            Home
                                        </div>
                                        <div class="w-12 py-3 text-center text-sm leading-6 font-semibold text-white/80">
                                        </div>
                                        <div class="flex-1 px-4 py-2 text-left text-sm leading-6 font-semibold text-white">
                                            Away
                                        </div>
                                    </div>

                                    @if ($result->is_overridden)
                                        <div class="px-6 py-4">
                                            <div class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-zinc-700">
                                                <span class="mt-2 block text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    This match was overridden by an admin.
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                            @foreach ($result->frames as $key => $frame)
                                                <div class="flex flex-wrap bg-white dark:bg-transparent">
                                                    <div
                                                        class="order-2 flex w-full border-y border-gray-200 dark:border-zinc-800/80 sm:order-first sm:w-auto sm:flex-1 sm:border-0">
                                                        @if ($frame->home_player_id)
                                                            <a href="{{ route('player.show', $frame->homePlayer) }}"
                                                                class="flex-1 border-0 px-4 py-2 text-sm leading-6 text-gray-900 focus:ring-0 focus:outline-0 dark:text-gray-100">
                                                                <span class="flex items-center gap-3">
                                                                    <img class="h-6 w-6 rounded-full object-cover"
                                                                        src="{{ $frame->homePlayer->avatar_url }}"
                                                                        alt="{{ $frame->homePlayer->name }} avatar">
                                                                    <span>{{ $frame->homePlayer->name }}</span>
                                                                </span>
                                                            </a>
                                                        @else
                                                            <div
                                                                class="flex-1 border-0 px-4 py-2 text-sm leading-6 text-gray-900 focus:ring-0 focus:outline-0 dark:text-gray-100">
                                                                <span class="flex items-center gap-3">
                                                                    <img class="h-6 w-6 rounded-full object-cover"
                                                                        src="{{ asset('/images/user.jpg') }}"
                                                                        alt="Awarded">
                                                                    <span>Awarded</span>
                                                                </span>
                                                            </div>
                                                        @endif
                                                        <div class="w-10 border-x border-gray-200 dark:border-zinc-800/80 sm:w-12">
                                                            <div
                                                                class="block w-full px-0 py-2 text-center text-sm leading-6 text-gray-900 focus:ring-0 focus:outline-0 dark:text-gray-100">
                                                                {{ $frame->home_score }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="order-first w-full bg-gray-50 px-4 py-2 text-left text-sm leading-6 font-semibold text-gray-900 dark:bg-zinc-800 dark:text-gray-100 sm:order-2 sm:w-12 sm:px-0 sm:text-center">
                                                        <span class="sm:hidden">Frame </span>{{ $key + 1 }}
                                                    </div>

                                                    <div class="order-last flex w-full sm:w-auto sm:flex-1">
                                                        <div class="order-last w-10 border-x border-gray-200 dark:border-zinc-800/80 sm:order-first sm:w-12">
                                                            <div
                                                                class="block w-full px-0 py-2 text-center text-sm leading-6 text-gray-900 focus:ring-0 focus:outline-0 dark:text-gray-100">
                                                                {{ $frame->away_score }}
                                                            </div>
                                                        </div>
                                                        @if ($frame->away_player_id)
                                                            <a href="{{ route('player.show', $frame->awayPlayer) }}"
                                                                class="order-first flex-1 border-0 px-4 py-2 text-sm leading-6 text-gray-900 focus:ring-0 focus:outline-0 dark:text-gray-100 sm:order-last">
                                                                <span class="flex items-center gap-3">
                                                                    <img class="h-6 w-6 rounded-full object-cover"
                                                                        src="{{ $frame->awayPlayer->avatar_url }}"
                                                                        alt="{{ $frame->awayPlayer->name }} avatar">
                                                                    <span>{{ $frame->awayPlayer->name }}</span>
                                                                </span>
                                                            </a>
                                                        @else
                                                            <div
                                                                class="order-first flex-1 border-0 px-4 py-2 text-sm leading-6 text-gray-900 focus:ring-0 focus:outline-0 dark:text-gray-100 sm:order-last">
                                                                <span class="flex items-center gap-3">
                                                                    <img class="h-6 w-6 rounded-full object-cover"
                                                                        src="{{ asset('/images/user.jpg') }}"
                                                                        alt="Awarded">
                                                                    <span>Awarded</span>
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="flex flex-wrap bg-gray-50 text-sm font-semibold text-gray-900 dark:bg-zinc-800/70 dark:text-gray-100">
                                                <div class="flex w-full border-b border-gray-200 dark:border-zinc-800/80 sm:w-auto sm:flex-1 sm:border-b-0">
                                                    <div class="flex-1 px-4 py-2 leading-6 sm:text-right">
                                                        Home total
                                                    </div>
                                                    <div class="w-10 border-x border-gray-200 py-2 text-center leading-6 dark:border-zinc-800/80 sm:w-12">
                                                        {{ $result->home_score }}
                                                    </div>
                                                </div>
                                                <div class="hidden w-12 bg-gray-50 dark:bg-zinc-800/70 sm:block"></div>
                                                <div class="flex w-full sm:w-auto sm:flex-1">
                                                    <div class="order-last w-10 border-x border-gray-200 py-2 text-center leading-6 dark:border-zinc-800/80 sm:order-first sm:w-12">
                                                        {{ $result->away_score }}
                                                    </div>
                                                    <div class="order-first flex-1 px-4 py-2 leading-6 sm:order-last">
                                                        Away total
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($result->is_confirmed && $result->submittedBy)
                                    @php
                                        $submittedAt = $result->submitted_at ?? $result->created_at;
                                    @endphp
                                    <p class="text-center text-sm italic text-gray-500 dark:text-gray-400">
                                        This result was submitted by {{ $result->submittedBy->name }} on
                                        {{ $submittedAt->format('l jS F Y') }} at
                                        {{ $submittedAt->format('H:i') }}.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
