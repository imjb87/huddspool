@extends('layouts.app')

@section('content')
    <div class="pt-[80px]">
        <div class="py-8 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <div class="border-b border-gray-200 pb-2 mb-4 flex items-center justify-between">
                    <div>
                        <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                            <h1 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">{{ $knockout->name }}</h1>
                            <p class="ml-2 mt-2 text-sm text-gray-500">{{ $knockout->season->name }}</p>
                        </div>
                    </div>
                </div>

                @php
                    $slotLabel = function ($match, $slot) use ($matchNumbers) {
                        $participant = $slot === 'home' ? $match->homeParticipant : $match->awayParticipant;

                        if ($participant) {
                            return $participant->display_name;
                        }

                        $previousMatch = $match->previousMatches->firstWhere('next_slot', $slot);

                        if ($previousMatch && isset($matchNumbers[$previousMatch->id])) {
                            return 'Winner of Match ' . $matchNumbers[$previousMatch->id];
                        }

                        $pairedParticipantId = $slot === 'home'
                            ? $match->away_participant_id
                            : $match->home_participant_id;

                        if ($pairedParticipantId) {
                            return 'Bye';
                        }

                        return 'TBC';
                    };
                @endphp
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 -mx-4 sm:mx-0">
                    @forelse ($knockout->rounds->where('is_visible', true) as $round)
                        <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 py-4 bg-green-700 flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-medium leading-6 text-white">{{ $round->name }}</h2>
                                </div>
                                <div class="text-xs font-semibold text-green-100">
                                    Best of {{ $round->bestOfValue() }} frames
                                </div>
                            </div>
                            <div class="border-t border-gray-200">
                                @forelse ($round->matches as $match)
                                    @php
                                        $matchLabel = isset($matchNumbers[$match->id]) ? 'Match ' . $matchNumbers[$match->id] : null;
                                        $homeLabel = $slotLabel($match, 'home');
                                        $awayLabel = $slotLabel($match, 'away');
                                        $hasBothParticipants = $match->home_participant_id && $match->away_participant_id;
                                    @endphp
                                    <div class="relative flex flex-wrap items-center justify-between px-4 py-4 text-sm text-gray-900 border-t border-gray-200 first:border-t-0">
                                        @if ($matchLabel)
                                            <div class="w-full sm:w-3/12 text-[11px] font-semibold uppercase tracking-wide text-gray-400 text-center sm:text-left mb-2 sm:mb-0">
                                                {{ $matchLabel }}
                                            </div>
                                        @endif
                                        <div class="flex flex-wrap w-full sm:w-6/12 items-center">
                                            <div class="w-5/12 text-center sm:text-right font-semibold">
                                                @php
                                                    $homeParticipant = $match->homeParticipant;
                                                @endphp
                                                    @if ($homeParticipant)
                                                    @if ($knockout->type === \App\KnockoutType::Doubles)
                                                        @php
                                                            $homePlayers = [$homeParticipant->playerOne, $homeParticipant->playerTwo];
                                                        @endphp
                                                        <div class="flex flex-col gap-0.5">
                                                            @foreach ($homePlayers as $player)
                                                                @if ($player)
                                                                    <a href="{{ route('player.show', $player) }}"
                                                                        class="hover:text-gray-500 transition">
                                                                        {{ $player->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-gray-500">TBC</span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @elseif ($homeParticipant->playerOne)
                                                        <a href="{{ route('player.show', $homeParticipant->playerOne) }}"
                                                            class="hover:text-gray-500 transition">
                                                            {{ $homeLabel }}
                                                        </a>
                                                    @elseif ($homeParticipant->team)
                                                        <a href="{{ route('team.show', $homeParticipant->team) }}"
                                                            class="hover:text-gray-500 transition">
                                                            {{ $homeLabel }}
                                                        </a>
                                                    @else
                                                        {{ $homeLabel }}
                                                    @endif
                                                @else
                                                    {{ $homeLabel }}
                                                @endif
                                            </div>
                                            <div class="w-2/12 text-center text-xs font-semibold text-gray-600">
                                                @if ($match->forfeitParticipant)
                                                    <span class="text-gray-500">FF</span>
                                                @elseif ($match->home_score !== null && $match->away_score !== null)
                                                    <span class="inline-flex bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold divide-x-2 divide-x-white">
                                                        <div class="w-1/2">{{ $match->home_score }}</div>
                                                        <div class="w-1/2">{{ $match->away_score }}</div>
                                                    </span>
                                                @elseif ($match->starts_at)
                                                    <span class="text-gray-500">{{ $match->starts_at->format('d/m') }}</span>
                                                @else
                                                    <span class="text-gray-400">Vs</span>
                                                @endif
                                            </div>
                                            <div class="w-5/12 text-center sm:text-left font-semibold">
                                                @php
                                                    $awayParticipant = $match->awayParticipant;
                                                @endphp
                                                @if ($awayParticipant)
                                                    @if ($knockout->type === \App\KnockoutType::Doubles)
                                                        @php
                                                            $awayPlayers = [$awayParticipant->playerOne, $awayParticipant->playerTwo];
                                                        @endphp
                                                        <div class="flex flex-col gap-0.5">
                                                            @foreach ($awayPlayers as $player)
                                                                @if ($player)
                                                                    <a href="{{ route('player.show', $player) }}"
                                                                        class="hover:text-gray-500 transition">
                                                                        {{ $player->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-gray-500">TBC</span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @elseif ($awayParticipant->playerOne)
                                                        <a href="{{ route('player.show', $awayParticipant->playerOne) }}"
                                                            class="hover:text-gray-500 transition">
                                                            {{ $awayLabel }}
                                                        </a>
                                                    @elseif ($awayParticipant->team)
                                                        <a href="{{ route('team.show', $awayParticipant->team) }}"
                                                            class="hover:text-gray-500 transition">
                                                            {{ $awayLabel }}
                                                        </a>
                                                    @else
                                                        {{ $awayLabel }}
                                                    @endif
                                                @else
                                                    {{ $awayLabel }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="w-full sm:w-3/12 text-[11px] font-semibold uppercase tracking-wide text-gray-400 text-center sm:text-right mt-2 sm:mt-0">
                                            <div>
                                                @if ($match->venue)
                                                    <a href="{{ route('venue.show', $match->venue) }}"
                                                        class="hover:text-gray-500 transition">
                                                        {{ $match->venue->name }}
                                                    </a>
                                                @else
                                                    Venue TBC
                                                @endif
                                            </div>
                                            @if ($match->referee)
                                                <div class="text-[11px]">
                                                    Referee: <span class="font-semibold">{{ $match->referee }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-4 text-sm text-gray-500">No matches scheduled for this round yet.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No rounds have been scheduled yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <x-logo-clouds />
    </div>
@endsection
