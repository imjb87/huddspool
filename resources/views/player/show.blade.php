@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-950">
        <div class="pb-10 lg:pb-14" data-player-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</h1>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                @if (session('status'))
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-6">
                    <section class="py-1" data-player-profile-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Player information</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Public profile details, current team information, and this season's playing record.
                                </p>
                            </div>

                            <div class="space-y-6 lg:col-span-2">
                                <div class="pt-1">
                                    <div class="space-y-5">
                                        <div class="flex items-start gap-8">
                                            <div class="shrink-0">
                                                <img class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700/80"
                                                    src="{{ $player->avatar_url }}"
                                                    alt="{{ $player->name }} avatar">
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</p>
                                                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</p>
                                                    </div>

                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                                                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $player->roleLabel() }}</p>
                                                    </div>

                                                    <div class="col-span-2 min-w-0">
                                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Team</p>
                                                        @if ($player->team)
                                                            <a href="{{ route('team.show', $player->team) }}"
                                                                class="mt-2 inline-flex max-w-full text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                                <span>{{ $player->team->name }}</span>
                                                            </a>
                                                        @else
                                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Free agent</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($averages)
                                            @php
                                                $framesWonPercentage = fmod((float) $averages->frames_won_percentage, 1.0) === 0.0
                                                    ? number_format($averages->frames_won_percentage, 0)
                                                    : number_format($averages->frames_won_percentage, 1);
                                                $framesLostPercentage = fmod((float) $averages->frames_lost_percentage, 1.0) === 0.0
                                                    ? number_format($averages->frames_lost_percentage, 0)
                                                    : number_format($averages->frames_lost_percentage, 1);
                                            @endphp
                                            <div class="pt-2 pb-2 sm:col-span-2">
                                                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800/80 dark:bg-zinc-900/75 dark:ring-1 dark:ring-white/5">
                                                    <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-zinc-800/80">
                                                        <div class="px-4 py-4 sm:px-5">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                                            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $averages->frames_played }}</p>
                                                        </div>
                                                        <div class="px-4 py-4 sm:px-5">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                                            <div class="mt-1 flex items-end justify-between gap-2">
                                                                <p class="text-base font-semibold text-green-700 dark:text-green-400">{{ $averages->frames_won }}</p>
                                                                <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">
                                                                    {{ $framesWonPercentage }}%
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="px-4 py-4 sm:px-5">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                                            <div class="mt-1 flex items-end justify-between gap-2">
                                                                <p class="text-base font-semibold text-red-700 dark:text-red-400">{{ $averages->frames_lost }}</p>
                                                                <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">
                                                                    {{ $framesLostPercentage }}%
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($player->email)
                                            <div class="sm:col-span-2">
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email address</p>
                                                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                                                    @auth
                                                        <a href="mailto:{{ $player->email }}"
                                                            class="underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                            {{ $player->email }}
                                                        </a>
                                                    @else
                                                        <span class="bg-black px-1 text-black">xxxxxxxxxxxxxxxxxx</span>
                                                    @endauth
                                                </p>
                                            </div>
                                        @endif

                                        @if ($player->telephone)
                                            <div class="sm:col-span-2">
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone number</p>
                                                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                                                    @auth
                                                        <a href="tel:{{ $player->telephone }}"
                                                            class="underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                            {{ $player->telephone }}
                                                        </a>
                                                    @else
                                                        <span class="bg-black px-1 text-black">xxxxxxxxxxxxxxxxxx</span>
                                                    @endauth
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    @if ($knockoutMatches->isNotEmpty())
                        <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-player-knockout-section>
                            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Knockouts</h3>
                                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                        Knockout matches this player has featured in.
                                    </p>
                                </div>

                                <div class="lg:col-span-2">
                                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                        @foreach ($knockoutMatches as $match)
                                            <a href="{{ route('knockout.show', $match->round->knockout) }}"
                                                class="block rounded-lg transition hover:bg-gray-50 dark:hover:bg-zinc-800/70"
                                                wire:key="player-knockout-{{ $match->id }}">
                                                <div class="flex items-start gap-3 py-4 sm:items-center sm:gap-4">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                                            <span>{{ $match->homeParticipant?->display_name ?? 'TBC' }}</span>
                                                            <span class="px-1 font-normal text-gray-400 dark:text-zinc-500">vs</span>
                                                            <span>{{ $match->awayParticipant?->display_name ?? 'TBC' }}</span>
                                                        </p>
                                                        <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                                            {{ $match->round?->knockout?->name ?? 'Knockout' }}
                                                            <span class="text-gray-300 dark:text-zinc-600">/</span>
                                                            {{ $match->round?->name ?? 'Round TBC' }}
                                                        </p>
                                                    </div>

                                                    <div class="shrink-0 text-right">
                                                        @if ($match->home_score !== null && $match->away_score !== null)
                                                            <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-gray-700 via-gray-600 to-gray-500 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                                                <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $match->home_score }}</div>
                                                                <div class="w-px bg-white/25"></div>
                                                                <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $match->away_score }}</div>
                                                            </div>
                                                        @else
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $match->starts_at ? $match->starts_at->format('j M') : 'Date TBC' }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    @if ($frames && $frames->isNotEmpty())
                        <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-player-frames-section>
                            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Frames</h3>
                                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                        Recent frames this player has played in the current section.
                                    </p>
                                </div>

                                <div class="lg:col-span-2">
                                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                        @foreach ($frames as $frame)
                                            @php
                                                $wonFrame = $frame->home_player_id === $player->id
                                                    ? $frame->home_score > $frame->away_score
                                                    : $frame->away_score > $frame->home_score;
                                                $opponentName = $frame->home_player_id === $player->id ? $frame->away_player_name : $frame->home_player_name;
                                                $opponentTeam = $frame->home_player_id === $player->id ? $frame->away_team_name : $frame->home_team_name;
                                            @endphp
                                            <a href="{{ route('result.show', $frame->result_id) }}"
                                                class="block rounded-lg px-3 hover:bg-gray-100 dark:hover:bg-zinc-800/70"
                                                wire:key="player-frame-{{ $frame->result_id }}-{{ $loop->index }}">
                                                <div class="flex items-center gap-4 py-4">
                                                    <div class="shrink-0">
                                                        <span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-full px-2 text-xs font-bold text-white shadow-sm ring-1 ring-black/10 {{ $wonFrame ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700' : 'bg-linear-to-br from-red-800 via-red-700 to-red-600' }}">
                                                            {{ $wonFrame ? 'W' : 'L' }}
                                                        </span>
                                                    </div>

                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $opponentName }}</p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $opponentTeam }}</p>
                                                    </div>

                                                    <div class="shrink-0 text-right text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $frame->fixture_date->format('j M') }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    @if ($history->isNotEmpty())
                        <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-player-history-section>
                            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                        Season-by-season playing history with archived team and section details.
                                    </p>
                                </div>

                                <div class="lg:col-span-2">
                                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                        @foreach ($history as $entry)
                                            @php
                                                $winPercentage = fmod((float) $entry['win_percentage'], 1.0) === 0.0
                                                    ? number_format($entry['win_percentage'], 0)
                                                    : number_format($entry['win_percentage'], 1);
                                                $lossPercentage = fmod((float) $entry['loss_percentage'], 1.0) === 0.0
                                                    ? number_format($entry['loss_percentage'], 0)
                                                    : number_format($entry['loss_percentage'], 1);
                                            @endphp
                                            <div class="flex items-center gap-4 py-4" wire:key="player-history-{{ $entry['season_id'] }}-{{ $entry['section_id'] }}-{{ md5((string) $entry['team_name']) }}">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['season_name'] }}</p>
                                                    <p class="mt-1 truncate text-sm text-gray-700 dark:text-gray-300">{{ $entry['team_name'] ?? 'Team TBC' }}</p>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $entry['section_name'] ?? 'Section TBC' }}</p>
                                                </div>

                                                <div class="ml-auto flex shrink-0 items-start gap-2 self-center text-center">
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                                        <p class="mt-1 text-sm font-semibold leading-5 text-gray-900 dark:text-gray-100">{{ $entry['played'] }}</p>
                                                        <span class="mt-1 inline-flex h-[18px] items-center text-[10px] font-semibold text-transparent">0%</span>
                                                    </div>
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                                        <p class="mt-1 text-sm font-semibold leading-5 text-green-700 dark:text-green-400">{{ $entry['wins'] }}</p>
                                                        <span class="mt-1 inline-flex items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">{{ $winPercentage }}%</span>
                                                    </div>
                                                    <div class="w-16">
                                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                                        <p class="mt-1 text-sm font-semibold leading-5 text-red-700 dark:text-red-400">{{ $entry['losses'] }}</p>
                                                        <span class="mt-1 inline-flex items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">{{ $lossPercentage }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
