@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-team-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $team->name }}</h1>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    <section class="py-1" data-team-info-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team information</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Current details for this team in the open season, including section, venue, and captain.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</p>
                                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $team->name }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Section</p>
                                        @if ($section)
                                            <a href="{{ route('ruleset.section.show', ['ruleset' => $section->ruleset, 'section' => $section]) }}"
                                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                {{ $section->name }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">No open section</p>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Venue</p>
                                        @if ($team->venue)
                                            <a href="{{ route('venue.show', $team->venue) }}"
                                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                {{ $team->venue->name }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Captain</p>
                                        @if ($team->captain)
                                            <a href="{{ route('player.show', $team->captain) }}"
                                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                                {{ $team->captain->name }}
                                            </a>
                                        @else
                                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Captain TBC</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-players-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Players</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Current squad members and their playing record in this section.
                                </p>
                            </div>

                            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80 lg:col-span-2">
                                @foreach ($players as $player)
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
                                    <a href="{{ route('player.show', $player) }}"
                                        class="block rounded-lg py-4 transition"
                                        wire:key="team-player-{{ $player->id }}">
                                        <div class="flex items-center gap-3 sm:gap-4">
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
                    </section>

                    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-fixtures-section>
                        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                            <div class="space-y-2">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixtures</h3>
                                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Current season fixtures and results for this team.
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                        @foreach ($fixtures as $fixture)
                                            @php
                                                $rowUrl = ($fixture->home_team_id == 1 || $fixture->away_team_id == 1)
                                                    ? null
                                                    : ($fixture->result_id ? route('result.show', $fixture->result_id) : route('fixture.show', $fixture->id));
                                                $isDraw = $fixture->result_id
                                                    && (int) $fixture->home_score === (int) $fixture->away_score;
                                                $teamWon = $fixture->result_id
                                                    && (($fixture->home_team_id == $team->id && (int) $fixture->home_score > (int) $fixture->away_score)
                                                    || ($fixture->away_team_id == $team->id && (int) $fixture->away_score > (int) $fixture->home_score));
                                                $resultPillClasses = $isDraw
                                                    ? 'bg-linear-to-br from-gray-600 via-gray-500 to-gray-400'
                                                    : ($teamWon
                                                        ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                                        : 'bg-linear-to-br from-red-800 via-red-700 to-red-600');
                                            @endphp
                                        <div wire:key="team-fixture-{{ $fixture->id }}">
                                            @if ($rowUrl)
                                                <a href="{{ $rowUrl }}" class="block rounded-lg transition">
                                            @endif
                                            <div class="flex items-start justify-between gap-4 py-4">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {{ $fixture->home_team_name }}
                                                        <span class="font-normal text-gray-400 dark:text-gray-500">vs</span>
                                                        {{ $fixture->away_team_name }}
                                                    </p>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ optional($fixture->fixture_date)->format('j M Y') ?? 'Date TBC' }}
                                                    </p>
                                                </div>

                                                <div class="ml-auto flex shrink-0 self-center items-center text-right">
                                                    @if ($fixture->result_id)
                                                        <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $resultPillClasses }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
                                                            <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $fixture->home_score ?? '' }}</div>
                                                            <div class="w-px bg-white/25"></div>
                                                            <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $fixture->away_score ?? '' }}</div>
                                                        </div>
                                                    @else
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ optional($fixture->fixture_date)->format('j M') ?? 'TBC' }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($rowUrl)
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    @if ($teamKnockoutMatches->isNotEmpty())
                        <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-knockout-section>
                            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team knockouts</h3>
                                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                        Recent team knockout ties and completed results.
                                    </p>
                                </div>

                                <div class="lg:col-span-2">
                                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                        @foreach ($teamKnockoutMatches as $match)
                                            @php
                                                $hasResult = $match->home_score !== null && $match->away_score !== null;
                                                $rowUrl = $hasResult ? route('knockout.show', $match->round->knockout) : null;
                                                $teamParticipantId = null;

                                                if ($match->homeParticipant?->team_id === $team->id) {
                                                    $teamParticipantId = $match->homeParticipant?->id;
                                                } elseif ($match->awayParticipant?->team_id === $team->id) {
                                                    $teamParticipantId = $match->awayParticipant?->id;
                                                }

                                                $wonMatch = $hasResult && $match->winner_participant_id && $teamParticipantId === $match->winner_participant_id;
                                                $isDraw = $hasResult && (int) $match->home_score === (int) $match->away_score;
                                                $resultPillClasses = $isDraw
                                                    ? 'bg-linear-to-br from-gray-600 via-gray-500 to-gray-400'
                                                    : ($wonMatch
                                                        ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700'
                                                        : 'bg-linear-to-br from-red-800 via-red-700 to-red-600');
                                            @endphp
                                            <div wire:key="team-knockout-{{ $match->id }}">
                                                @if ($rowUrl)
                                                    <a href="{{ $rowUrl }}" class="block rounded-lg transition">
                                                @endif
                                                <div class="flex items-start gap-3 py-4 sm:items-center sm:gap-4">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="[overflow-wrap:anywhere] text-sm leading-5 font-semibold text-gray-900 dark:text-gray-100">
                                                            <span>{{ $match->homeParticipant?->display_name ?? 'TBC' }}</span>
                                                            <span class="px-1 font-normal text-gray-400 dark:text-gray-500">vs</span>
                                                            <span>{{ $match->awayParticipant?->display_name ?? 'TBC' }}</span>
                                                        </p>
                                                        <p class="mt-1 [overflow-wrap:anywhere] text-xs leading-5 text-gray-500 dark:text-gray-400">
                                                            {{ $match->round?->knockout?->name ?? 'Knockout' }}
                                                            <span class="text-gray-300 dark:text-zinc-600">/</span>
                                                            {{ $match->round?->name ?? 'Round TBC' }}
                                                        </p>
                                                    </div>

                                                    <div class="shrink-0 text-right">
                                                        @if ($hasResult)
                                                            <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full {{ $resultPillClasses }} text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10">
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
                                                @if ($rowUrl)
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    @if ($history->isNotEmpty())
                        <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-history-section>
                            <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                                <div class="space-y-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">History</h3>
                                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                        Season-by-season record for this team across previous campaigns.
                                    </p>
                                </div>

                                <div class="lg:col-span-2">
                                    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                                        @foreach ($history as $entry)
                                            @php
                                                $historyLink = $entry['ruleset_slug']
                                                    ? route('history.show', ['season' => $entry['season_slug'], 'ruleset' => $entry['ruleset_slug']])
                                                    : null;
                                            @endphp
                                            <div wire:key="team-history-{{ $entry['season_id'] }}-{{ $entry['ruleset_id'] }}">
                                                @if ($historyLink)
                                                    <a href="{{ $historyLink }}" class="block rounded-lg transition">
                                                @endif
                                                <div class="flex items-center gap-4 py-4">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['season_name'] }}</p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $entry['ruleset_name'] ?? 'Ruleset TBC' }}</p>
                                                    </div>

                                                    <div class="ml-auto flex shrink-0 items-center gap-2 text-center">
                                                        <div class="w-11">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pl</p>
                                                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['played'] }}</p>
                                                        </div>
                                                        <div class="w-11">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">W</p>
                                                            <p class="mt-1 text-sm font-semibold text-green-700 dark:text-green-400">{{ $entry['wins'] }}</p>
                                                        </div>
                                                        <div class="w-11">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">D</p>
                                                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['draws'] }}</p>
                                                        </div>
                                                        <div class="w-11">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">L</p>
                                                            <p class="mt-1 text-sm font-semibold text-red-700 dark:text-red-400">{{ $entry['losses'] }}</p>
                                                        </div>
                                                        <div class="w-11">
                                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pts</p>
                                                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $entry['points'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($historyLink)
                                                    </a>
                                                @endif
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

        <x-logo-clouds  />
    </div>
@endsection
