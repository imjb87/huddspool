@extends('layouts.app')

@section('content')
<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                    <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Team profile</h3>
                </div>
            </div>
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="sm:self-start w-full lg:w-1/3 gap-y-6 flex flex-col">
                    <div class="overflow-hidden bg-white shadow rounded-lg">
                        <div class="md:flex md:items-center md:justify-between md:space-x-5 px-4 py-6 sm:px-6">
                            <div class="flex items-start space-x-5">
                                <div class="pt-1.5">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h1>
                                    <p class="text-sm font-medium text-gray-500">
                                        @if( $team->section() )
                                        <a href="{{ route('table.index', $team->section()->ruleset) }}">
                                            {{ $team->section()->name }}
                                        </a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100">
                            <dl class="divide-y divide-gray-100">
                                <a href="{{ route('venue.show', $team->venue->id) }}"
                                    class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Venue</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $team->venue->name }}
                                    </dd>
                                </a>
                                @if ($team->captain)
                                <a href="{{ route('player.show', $team->captain->id) }}"
                                    class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Captain</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $team->captain->name }}
                                    </dd>
                                </a>
                                @endif
                            </dl>
                        </div>
                    </div>
                    <div class="bg-green-700 shadow rounded-md sm:rounded-lg overflow-hidden">
                        <div class="px-4 sm:px-6 py-4">
                            <h2 class="text-sm font-medium leading-6 text-white">Players</h2>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="overflow-hidden">
                                <div class="min-w-full divide-y divide-gray-300">
                                    <div class="bg-gray-50">
                                        <div class="flex">
                                            <div scope="col"
                                                class="py-2 px-4 sm:px-6 text-left text-sm font-semibold text-gray-900 w-6/12">
                                                Name</div>
                                            <div scope="col"
                                                class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                Pl</div>
                                            <div scope="col"
                                                class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                W</div>
                                            <div scope="col"
                                                class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                L</div>
                                        </div>
                                    </div>
                                    <div class="divide-y divide-gray-200 bg-white">
                                        @foreach ($players as $player)
                                        <a href="{{ route('player.show', $player->id) }}"
                                            class=" hover:cursor-pointer hover:bg-gray-50 flex">
                                            <div
                                                class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm text-gray-900 w-6/12 truncate">
                                                {{ $player->name }}
                                            </div>
                                            <div class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                {{ $player->frames_played }}
                                            </div>
                                            <div class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                {{ $player->frames_won }}
                                            </div>
                                            <div class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                {{ $player->frames_lost }}
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <section class="w-full lg:w-2/3">
                    <div class="bg-white shadow rounded-lg flex flex-col overflow-hidden">
                        <div class="px-4 py-4 sm:px-6 bg-green-700">
                            <h2 class="text-sm font-medium leading-6 text-white">Fixtures &amp; Results</h2>
                        </div>
                        <div class="border-t border-gray-200 h-full flex flex-col">
                            <div class="min-w-full overflow-hidden">
                                <div class="bg-gray-50 flex">
                                    <div scope="col"
                                        class="px-2 py-2 text-right text-sm font-semibold text-gray-900 w-5/12">Home
                                    </div>
                                    <div scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-1/12"></div>
                                    <div scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-1/12">
                                    </div>
                                    <div scope="col"
                                        class="px-2 py-2 text-left text-sm font-semibold text-gray-900 w-5/12">Away
                                    </div>
                                </div>
                                <div class="bg-white">
                                    @foreach ($fixtures as $fixture)
                                    @if ($fixture->home_team_id == 1 || $fixture->away_team_id == 1)
                                    <div class="flex w-full border-t border-gray-300 bg-gray-50">
                                        @else
                                        <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                                            href="{{ $fixture->result_id ? route('result.show', $fixture->result_id) : route('fixture.show', $fixture->id) }}">
                                            @endif
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-right w-5/12 {{ $fixture->home_team_shortname ? 'hidden md:block' : '' }}">
                                                {{ $fixture->home_team_name }}
                                            </div>
                                            @if ($fixture->home_team_shortname)
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-right w-5/12 md:hidden">
                                                {{ $fixture->home_team_shortname }}
                                            </div>
                                            @endif
                                            @if ($fixture->result_id)
                                            <div
                                                class="whitespace-nowrap px-1 py-3 text-sm text-gray-500 text-right font-semibold w-2/12 flex">
                                                <div
                                                    class="inline-flex bg-green-700 text-white text-center mx-auto text-xs leading-7 min-w-[44px] font-extrabold divide-x-2 divide-x-white">
                                                    <div class="w-1/2">{{ $fixture->home_score ?? '' }}
                                                    </div>
                                                    <div class="w-1/2">{{ $fixture->away_score ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-center font-semibold w-2/12">
                                                {{ optional($fixture->fixture_date)->format('d/m') }}
                                            </div>
                                            @endif
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-left w-5/12 {{ $fixture->away_team_shortname ? 'hidden md:block' : '' }}">
                                                {{ $fixture->away_team_name }}
                                            </div>
                                            @if ($fixture->away_team_shortname)
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-900 text-left w-5/12 md:hidden">
                                                {{ $fixture->away_team_shortname }}
                                            </div>
                                            @endif
                                            @if ($fixture->home_team_id == 1 || $fixture->away_team_id == 1)
                                    </div>
                                    @else
                                    </a>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    @if ($history->isNotEmpty())
        <div class="py-8 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 lg:px-8">
                <section>
                    <div class="bg-white shadow sm:rounded-lg flex flex-col h-full overflow-hidden -mx-4 sm:mx-0">
                        <div class="px-4 py-4 sm:px-6 bg-green-700 flex items-center justify-between">
                            <h2 class="text-sm font-medium leading-6 text-white">History</h2>
                        </div>
                        <div class="border-t border-gray-200 h-full flex flex-col">
                            <div class="min-w-full overflow-hidden">
                                <div class="bg-gray-50 flex">
                                    <div class="flex w-1/2 pl-4 sm:pl-6">
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-6/12">Season</div>
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-6/12">Ruleset</div>
                                    </div>
                                    <div class="flex w-1/2">
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">Pl</div>
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">W</div>
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">D</div>
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">L</div>
                                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">Pts</div>
                                    </div>
                                </div>
                                <div class="bg-white overflow-hidden">
                                    @foreach ($history as $entry)
                                        @php
                                            $historyLink = $entry['ruleset_slug']
                                                ? route('history.show', ['season' => $entry['season_slug'], 'ruleset' => $entry['ruleset_slug']])
                                                : null;
                                        @endphp
                                        @if ($historyLink)
                                            <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                                                href="{{ $historyLink }}">
                                        @else
                                            <div class="flex w-full border-t border-gray-300">
                                        @endif
                                                <div class="flex w-1/2 pl-4 sm:pl-6 items-center">
                                                    <div class="whitespace-nowrap py-2 text-sm text-gray-900 w-6/12 font-semibold" title="{{ $entry['season_name'] }}">
                                                        {{ $entry['season_label'] ?? $entry['season_name'] }}
                                                    </div>
                                                    <div class="whitespace-nowrap py-2 text-sm text-gray-500 w-6/12 truncate">
                                                        {{ $entry['ruleset_name'] ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="flex w-1/2 items-center">
                                                    <div class="py-2 text-sm text-gray-900 w-1/5 text-center">
                                                        {{ $entry['played'] }}
                                                    </div>
                                                    <div class="py-2 text-sm text-gray-900 w-1/5 text-center">
                                                        {{ $entry['wins'] }}
                                                    </div>
                                                    <div class="py-2 text-sm text-gray-900 w-1/5 text-center">
                                                        {{ $entry['draws'] }}
                                                    </div>
                                                    <div class="py-2 text-sm text-gray-900 w-1/5 text-center">
                                                        {{ $entry['losses'] }}
                                                    </div>
                                                    <div class="py-2 text-sm text-center font-semibold text-green-700 w-1/5">
                                                        {{ $entry['points'] }}
                                                    </div>
                                                </div>
                                        @if ($historyLink)
                                            </a>
                                        @else
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    @endif
</div>
@endsection
