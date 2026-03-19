@extends('layouts.app')

@section('content')
    @php
        $canSubmitResult = auth()->check() && auth()->user()->can('submitResult', $fixture) && (! $fixture->result || ! $fixture->result->is_confirmed);
        $submissionIsOpen = $canSubmitResult && $fixture->fixture_date->lte(now());
        $standings = $fixture->section->standings()->filter(function ($standing) use ($fixture) {
            return (int) $standing->id === (int) $fixture->home_team_id || (int) $standing->id === (int) $fixture->away_team_id;
        })->values();
    @endphp

    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-950">
        <div class="pb-10 lg:pb-14" data-fixture-page>
            @if ($canSubmitResult)
                <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-900/60 dark:bg-red-950/40">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-red-700 dark:text-red-300">Result submission</p>
                            <p class="text-sm text-red-900 dark:text-red-100">{{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}</p>
                        </div>

                        @if ($submissionIsOpen)
                            <a href="{{ route('result.create', $fixture) }}"
                                class="inline-flex items-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-red-800 hover:via-red-700 hover:to-red-600">
                                {{ $fixture->result ? 'Submit result' : 'Submit result' }}
                            </a>
                        @else
                            <span
                                class="inline-flex items-center rounded-full bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 dark:bg-zinc-800 dark:text-gray-300">
                                Opens {{ $fixture->fixture_date->format('j M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fixture</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $fixture->section->name }}</p>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    <section class="py-1" data-fixture-info-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixture information</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Match details, venue, and links back to the wider section schedule.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</p>
                                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $fixture->homeTeam->name }} <span class="font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                            {{ $fixture->awayTeam->name }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $fixture->fixture_date->format('l jS F Y') }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ruleset</p>
                                        <a href="{{ route('ruleset.section.show', ['ruleset' => $fixture->section->ruleset, 'section' => $fixture->section, 'tab' => 'fixtures-results']) }}"
                                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                            {{ $fixture->section->ruleset->name }}
                                        </a>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</p>
                                        @if ($fixture->venue)
                                            <a href="{{ route('venue.show', $fixture->venue) }}"
                                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                {{ $fixture->venue->name }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-fixture-head-to-head-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Head to head</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Current section standings for the two teams in this fixture.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                    @foreach ($standings as $standing)
                                        <a href="{{ route('team.show', $standing->id) }}"
                                            class="block"
                                            wire:key="fixture-standing-{{ $standing->id }}">
                                            <div class="flex items-center gap-4 py-4">
                                                <div class="w-8 shrink-0 text-sm font-semibold text-gray-500 dark:text-gray-400">
                                                    {{ $loop->iteration }}
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->name }}</p>
                                                </div>

                                                <div class="ml-auto flex shrink-0 items-center gap-5 text-center">
                                                    <div class="w-12">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pl</p>
                                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->played }}</p>
                                                    </div>
                                                    <div class="hidden w-12 md:block">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">W</p>
                                                        <p class="mt-1 text-sm font-semibold text-green-700 dark:text-green-400">{{ $standing->wins }}</p>
                                                    </div>
                                                    <div class="hidden w-12 md:block">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">D</p>
                                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->draws }}</p>
                                                    </div>
                                                    <div class="hidden w-12 md:block">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">L</p>
                                                        <p class="mt-1 text-sm font-semibold text-red-700 dark:text-red-400">{{ $standing->losses }}</p>
                                                    </div>
                                                    <div class="w-12">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pts</p>
                                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->points }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-fixture-home-team-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $fixture->homeTeam->name }}</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Current player records for the home side in this section.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                    @foreach ($home_team_players as $player)
                                        @php
                                            $framesPlayed = (int) $player->frames_played;
                                            $framesWon = (int) $player->frames_won;
                                            $framesLost = (int) $player->frames_lost;
                                            $rawWonPercentage = $framesPlayed > 0 ? ($framesWon / $framesPlayed) * 100 : 0;
                                            $rawLostPercentage = $framesPlayed > 0 ? ($framesLost / $framesPlayed) * 100 : 0;
                                            $wonPercentage = fmod($rawWonPercentage, 1.0) === 0.0
                                                ? number_format($rawWonPercentage, 0)
                                                : number_format($rawWonPercentage, 1);
                                            $lostPercentage = fmod($rawLostPercentage, 1.0) === 0.0
                                                ? number_format($rawLostPercentage, 0)
                                                : number_format($rawLostPercentage, 1);
                                        @endphp
                                        <a href="{{ route('player.show', $player->id) }}"
                                            class="block"
                                            wire:key="fixture-home-player-{{ $player->id }}">
                                            <div class="flex items-center gap-3 py-4 sm:gap-4">
                                                <div class="shrink-0">
                                                    <img class="h-8 w-8 rounded-full object-cover"
                                                        src="{{ $player->avatar_url }}"
                                                        alt="{{ $player->name }} avatar">
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</p>
                                                </div>

                                                <div class="ml-auto flex shrink-0 items-center gap-3 text-center sm:gap-4">
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                                        <div class="mt-1 flex flex-col items-center gap-1">
                                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $framesPlayed }}</p>
                                                            <span class="invisible inline-flex items-center rounded-md px-1 py-0.5 text-[10px] font-semibold">0%</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                                        <div class="mt-1 flex flex-col items-center gap-1">
                                                            <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $framesWon }}</p>
                                                            <span class="inline-flex items-center rounded-md bg-green-100 px-1 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">{{ $wonPercentage }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                                        <div class="mt-1 flex flex-col items-center gap-1">
                                                            <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $framesLost }}</p>
                                                            <span class="inline-flex items-center rounded-md bg-red-100 px-1 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">{{ $lostPercentage }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-fixture-away-team-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $fixture->awayTeam->name }}</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Current player records for the away side in this section.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                    @foreach ($away_team_players as $player)
                                        @php
                                            $framesPlayed = (int) $player->frames_played;
                                            $framesWon = (int) $player->frames_won;
                                            $framesLost = (int) $player->frames_lost;
                                            $rawWonPercentage = $framesPlayed > 0 ? ($framesWon / $framesPlayed) * 100 : 0;
                                            $rawLostPercentage = $framesPlayed > 0 ? ($framesLost / $framesPlayed) * 100 : 0;
                                            $wonPercentage = fmod($rawWonPercentage, 1.0) === 0.0
                                                ? number_format($rawWonPercentage, 0)
                                                : number_format($rawWonPercentage, 1);
                                            $lostPercentage = fmod($rawLostPercentage, 1.0) === 0.0
                                                ? number_format($rawLostPercentage, 0)
                                                : number_format($rawLostPercentage, 1);
                                        @endphp
                                        <a href="{{ route('player.show', $player->id) }}"
                                            class="block"
                                            wire:key="fixture-away-player-{{ $player->id }}">
                                            <div class="flex items-center gap-3 py-4 sm:gap-4">
                                                <div class="shrink-0">
                                                    <img class="h-8 w-8 rounded-full object-cover"
                                                        src="{{ $player->avatar_url }}"
                                                        alt="{{ $player->name }} avatar">
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</p>
                                                </div>

                                                <div class="ml-auto flex shrink-0 items-center gap-3 text-center sm:gap-4">
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                                        <div class="mt-1 flex flex-col items-center gap-1">
                                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $framesPlayed }}</p>
                                                            <span class="invisible inline-flex items-center rounded-md px-1 py-0.5 text-[10px] font-semibold">0%</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                                        <div class="mt-1 flex flex-col items-center gap-1">
                                                            <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $framesWon }}</p>
                                                            <span class="inline-flex items-center rounded-md bg-green-100 px-1 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">{{ $wonPercentage }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                                        <div class="mt-1 flex flex-col items-center gap-1">
                                                            <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $framesLost }}</p>
                                                            <span class="inline-flex items-center rounded-md bg-red-100 px-1 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">{{ $lostPercentage }}%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
