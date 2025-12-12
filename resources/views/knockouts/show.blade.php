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

                <div class="grid grid-cols-1 gap-x-6 gap-y-6 -mx-4 sm:mx-0">
                    @forelse ($knockout->rounds as $round)
                        <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 py-4 bg-green-700 flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-medium leading-6 text-white">{{ $round->name }}</h2>
                                </div>
                                <div class="text-xs font-semibold text-green-100">
                                    Best of {{ $round->bestOfValue() }} frames
                                </div>
                            </div>
                            @php
                                $matchesByVenue = $round->matches->sortBy(fn ($match) => $match->venue?->name ?? 'TBC')->groupBy(function ($match) {
                                    return $match->venue?->name ?? 'TBC Venue';
                                });
                            @endphp
                            <div class="border-t border-gray-200">
                                @forelse ($matchesByVenue as $venueName => $matches)
                                    <div class="border-b border-gray-200 last:border-b-0">
                                        <div class="bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-600 uppercase tracking-wide flex justify-center items-center">
                                            <span>{{ $venueName }}</span>
                                        </div>
                                        @foreach ($matches as $match)
                                            <div class="relative flex flex-wrap items-center justify-between px-4 py-3 text-sm text-gray-900 border-t border-gray-200 first:border-t-0">
                                                <div class="w-5/12 text-center sm:text-right font-semibold">
                                                    {{ $match->homeParticipant?->display_name ?? 'TBC' }}
                                                </div>
                                                <div class="w-2/12 text-center text-xs font-semibold text-gray-600">
                                                    @if ($match->home_score !== null && $match->away_score !== null)
                                                        <span class="inline-flex bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold divide-x-2 divide-x-white">
                                                            <div class="w-1/2">{{ $match->home_score }}</div><div class="w-1/2">{{ $match->away_score }}</div>
                                                        </span>
                                                    @elseif ($match->starts_at)
                                                        <span class="text-gray-500">{{ $match->starts_at->format('j/m') }}</span>
                                                    @else
                                                        <span class="text-gray-400">Vs</span>
                                                    @endif
                                                </div>
                                                <div class="w-5/12 text-center sm:text-left font-semibold">
                                                    {{ $match->awayParticipant?->display_name ?? 'TBC' }}
                                                </div>
                                                <div class="w-full text-center sm:text-right text-xs mt-2 sm:mt-0 sm:absolute sm:top-3.5 sm:right-4">
                                                    @if ($match->userCanSubmit(auth()->user()) && ! $match->completed_at)
                                                        <a href="{{ route('knockout.matches.submit', $match) }}"
                                                            class="font-semibold bg-green-700 hover:bg-green-800 py-1 px-2 rounded text-white">Submit result</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
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
