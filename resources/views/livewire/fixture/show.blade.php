<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">
                    {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}</h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="w-full lg:w-1/3 self-start flex flex-col gap-y-6">
                    @if ($isCaptain && !$fixture->result && $fixture->fixture_date->lte(now()))
                        <a href="{{ route('result.create', $fixture->id) }}"
                            class="block items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 text-center">
                            Submit Result
                        </a>
                    @endif
                    <dl class="bg-white flex flex-wrap rounded-lg shadow-sm ring-1 ring-gray-900/5">
                        <div class="flex-auto pl-6 pt-6">
                            <dt class="text-sm font-semibold leading-6 text-gray-900">Section</dt>
                            <dd class="mt-1 text-base font-semibold leading-6 text-gray-900">
                                {{ $fixture->section->name }}
                            </dd>
                        </div>
                        <div class="mt-6 flex w-full flex-none gap-x-4 border-t border-gray-900/5 px-6 pt-6">
                            <dt class="flex w-5">
                                <span class="sr-only">Fixture date</span>
                                <svg class="w-4 text-gray-400 mx-auto" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M128 0c17.7 0 32 14.3 32 32V64H288V32c0-17.7 14.3-32 32-32s32 14.3 32 32V64h48c26.5 0 48 21.5 48 48v48H0V112C0 85.5 21.5 64 48 64H96V32c0-17.7 14.3-32 32-32zM0 192H448V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V192zm64 80v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16zm128 0v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H208c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V272c0-8.8-7.2-16-16-16H336zM64 400v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H80c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H208zm112 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V400c0-8.8-7.2-16-16-16H336c-8.8 0-16 7.2-16 16z" />
                                </svg>
                            </dt>
                            <dd class="text-sm font-medium leading-6 text-gray-900">
                                <date>{{ $fixture->fixture_date->format('l jS F Y') }}</date>
                            </dd>
                        </div>
                        <div class="mt-4 flex w-full flex-none gap-x-4 px-6 pb-6">
                            <dt class="flex w-5">
                                <span class="sr-only">Venue</span>
                                <svg class="w-4 text-gray-400 mx-auto" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                                </svg>
                            </dt>
                            <dd class="text-sm leading-6 text-gray-900">
                                <a class="text-sm font-medium leading-6 text-gray-900 hover:underline"
                                    href="{{ route('venue.show', $fixture->venue->id) }}">
                                    {{ $fixture->venue->name }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
                <div class="w-full lg:w-2/3 flex flex-col gap-y-6">
                    @if ($fixture->result)
                        <div class="overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                            <div class="bg-white hidden sm:flex">
                                <div class="flex-1 leading-6 py-3 px-4 sm:px-6 text-right font-semibold text-gray-900">
                                    {{ $fixture->homeTeam->name }}
                                </div>
                                <div class="w-12 text-center text-sm leading-6 py-3 font-semibold text-gray-900">
                                    vs
                                </div>
                                <div class="flex-1 leading-6 py-3 px-4 sm:px-6 text-left font-semibold text-gray-900">
                                    {{ $fixture->awayTeam->name }}
                                </div>
                            </div>
                            @foreach ($fixture->result->frames as $key => $frame)
                                <div class="flex flex-wrap bg-white">
                                    <div
                                        class="w-full sm:w-auto flex sm:flex-1 order-2 sm:order-first border-b border-t border-gray-200 sm:border-0">
                                        <div
                                            class="border-0 py-2 px-4 sm:px-6 leading-6 text-sm flex-1 focus:outline-0 focus:ring-0">
                                            {{ $frame->homePlayer->name ?? 'Awarded' }}
                                        </div>
                                        <div class="w-10 sm:w-12 border-x border-gray-200">
                                            <div
                                                class="block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0">
                                                {{ $frame->home_score }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="w-full sm:w-12 sm:text-center py-2 px-4 sm:px-0 text-left text-sm font-semibold text-gray-900 order-first sm:order-2 leading-6">
                                        <span class="sm:hidden">Frame </span>
                                        {{ $key + 1 }}
                                    </div>
                                    <div class="w-full sm:w-auto flex sm:flex-1 order-last">
                                        <div class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200">
                                            <div
                                                class="block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0">
                                                {{ $frame->away_score }}
                                            </div>
                                        </div>
                                        <div
                                            class="border-0 py-2 px-4 sm:px-6 leading-6 text-sm flex-1 order-first sm:order-last focus:outline-0 focus:ring-0">
                                            {{ $frame->awayPlayer->name ?? 'Awarded' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex flex-wrap bg-white font-semibold text-gray-900 text-sm">
                                <div class="w-full sm:w-auto flex sm:flex-1 border-b border-gray-200">
                                    <div class="flex-1 leading-6 py-2 px-4 sm:px-6 sm:text-right">
                                        Home total
                                    </div>
                                    <div class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200">
                                        {{ $fixture->result->home_score }}
                                    </div>
                                </div>
                                <div class="w-10 sm:w-12 bg-gray-50"></div>
                                <div class="w-full sm:w-auto flex sm:flex-1">
                                    <div
                                        class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200 order-last sm:order-first">
                                        {{ $fixture->result->away_score }}
                                    </div>
                                    <div class="flex-1 leading-6 py-2 px-4 sm:px-6 order-first sm:order-last">
                                        Away total
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white shadow-md rounded-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 py-4 bg-green-700">
                                <h2 class="text-sm font-medium leading-6 text-white">Current standings</h2>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="w-full max-w-full overflow-hidden">
                                    <div class="bg-gray-50">
                                        <div class="flex">
                                            <div scope="col"
                                                class="px-3 py-2 text-center text-sm font-semibold text-gray-900 w-1/12">
                                                #
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 sm:px-3 text-left text-sm font-semibold text-gray-900 w-7/12 md:w-6/12">
                                                Team</div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                Pl
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:block w-1/12">
                                                W
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:block w-1/12">
                                                D</div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:block w-1/12">
                                                L</div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                Pts</div>
                                            </tr>
                                        </div>
                                        <div class="bg-white">
                                            @foreach ($fixture->section->standings() as $team)
                                                @if ($team->id == $fixture->homeTeam->id || $team->id == $fixture->awayTeam->id)
                                                    <a href="{{ route('team.show', $team->id) }}"
                                                        class="border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 flex">
                                                        <div
                                                            class="whitespace-nowrap py-2 px-3 text-sm font-medium text-gray-900 text-center w-1/12">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                        <div
                                                            class="px-2 sm:px-3 py-2 text-sm font-medium text-gray-900 truncate w-7/12 md:w-6/12">
                                                            <span
                                                                class="{{ $team->shortname ? 'hidden md:inline' : '' }}">{{ $team->name }}</span>
                                                            @if ($team->shortname)
                                                                <span class="md:hidden"
                                                                    href="{{ route('team.show', $team->id) }}">{{ $team->shortname }}</span>
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center w-2/12 md:w-1/12">
                                                            {{ $team->played }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center hidden md:block w-1/12">
                                                            {{ $team->wins }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center hidden md:block w-1/12">
                                                            {{ $team->draws }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center hidden md:block w-1/12">
                                                            {{ $team->losses }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center w-2/12 md:w-1/12">
                                                            {{ $team->points }}</div>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-700 shadow rounded-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 sm:px-6 py-4">
                                <h2 class="text-sm font-medium leading-6 text-white">{{ $fixture->homeTeam->name }}
                                </h2>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="overflow-hidden">
                                    <div class="min-w-full divide-y divide-gray-300">
                                        <div class="bg-gray-50">
                                            <div class="flex">
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-left text-sm font-semibold text-gray-900 w-6/12">
                                                    Name</div>
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                    Pl</div>
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                    W</div>
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                    L</div>
                                            </div>
                                        </div>
                                        <div class="divide-y divide-gray-200 bg-white">
                                            @foreach ($fixture->homeTeam->players as $player)
                                                <a href="{{ route('player.show', $player->id) }}"
                                                    class=" hover:cursor-pointer hover:bg-gray-50 flex">
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-6/12 truncate">
                                                        {{ $player->name }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                        {{ $player->framesPlayed->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                        {{ $player->framesWon->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                        {{ $player->framesLost->count() }}
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-700 shadow rounded-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 sm:px-6 py-4">
                                <h2 class="text-sm font-medium leading-6 text-white">
                                    {{ $fixture->awayTeam->name }}</h2>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="overflow-hidden">
                                    <div class="min-w-full divide-y divide-gray-300">
                                        <div class="bg-gray-50">
                                            <div class="flex">
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-left text-sm font-semibold text-gray-900 w-6/12">
                                                    Name</div>
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                    Pl</div>
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                    W</div>
                                                <div scope="col"
                                                    class="py-3.5 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12">
                                                    L</div>
                                            </div>
                                        </div>
                                        <div class="divide-y divide-gray-200 bg-white">
                                            @foreach ($fixture->awayTeam->players as $player)
                                                <a href="{{ route('player.show', $player->id) }}"
                                                    class=" hover:cursor-pointer hover:bg-gray-50 flex">
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-6/12 truncate">
                                                        {{ $player->name }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                        {{ $player->framesPlayed->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                        {{ $player->framesWon->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 text-center">
                                                        {{ $player->framesLost->count() }}
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<x-logo-clouds />
</div>
