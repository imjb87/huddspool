<div class="mt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                    <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Fixture</h3>
                    <p class="ml-2 mt-1 truncate text-sm text-gray-500">{{ $fixture->section->name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="w-full lg:w-1/3">
                    @if ($isTeamAdmin && !$fixture->result)
                        @if ($fixture->fixture_date->lte(now()))
                            <a href="{{ route('result.create', $fixture->id) }}"
                                class="block w-full mb-4 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 text-center">
                                Submit Result
                            </a>
                        @else
                            <button disabled
                                class="tooltip w-full mb-4 block items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 text-center">
                                <div class="tooltip-text">Oops! You're a bit early! Come back to submit a result from
                                    {{ $fixture->fixture_date->format('l jS F Y') }}</div>
                                Submit Result
                            </button>
                        @endif
                    @endif
                    <div class="overflow-hidden bg-white shadow rounded-lg">
                        <div class="md:flex md:items-center md:justify-between md:space-x-5 px-4 py-6 sm:px-6">
                            <div class="flex items-start space-x-5">
                                <div class="pt-1.5">
                                    <h1 class="text-base font-semibold leading-6 text-gray-900">
                                        {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100">
                            <dl class="divide-y divide-gray-100">
                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Date</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        <date>{{ $fixture->fixture_date->format('l jS F Y') }}</date>
                                    </dd>
                                </div>
                                <a href="{{ route('ruleset.show', $fixture->section->ruleset) }}" class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Ruleset</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $fixture->section->ruleset->name }}</dd>
                                </a>
                                <a href="{{ route('venue.show', $fixture->venue) }}" class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Venue</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $fixture->venue->name }}</dd>
                                </a>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-2/3 flex flex-col gap-y-6">
                    @if ($fixture->result)
                        <div>
                            <div class="overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                                <div class="bg-green-700 hidden sm:flex">
                                    <div class="flex-1 leading-6 py-2 px-4 text-right font-semibold text-white text-sm">
                                        Home
                                    </div>
                                    <div class="w-12 text-center text-sm leading-6 py-3 font-semibold text-gray-900">
                                    </div>
                                    <div class="flex-1 leading-6 py-2 px-4 text-left font-semibold text-white text-sm">
                                        Away
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
                                            class="w-full sm:w-12 sm:text-center py-2 px-4 sm:px-0 text-left text-sm font-semibold bg-green-700 sm:bg-gray-50 text-white sm:text-gray-900 order-first sm:order-2 leading-6">
                                            <span class="sm:hidden">Frame </span>
                                            {{ $key + 1 }}
                                        </div>
                                        <div class="w-full sm:w-auto flex sm:flex-1 order-last">
                                            <div
                                                class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200">
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
                                <div class="flex flex-wrap bg-gray-50 font-semibold text-gray-900 text-sm">
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
                            <!-- submitted by -->
                            <div class="my-3 px-4 sm:px-6">
                                <p class="italic text-sm text-center mx-auto">This result was submitted by
                                    {{ $fixture->result->submittedBy->name }} on
                                    {{ $fixture->result->created_at->format('l jS F Y') }} at
                                    {{ $fixture->result->created_at->format('H:i') }}.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white shadow rounded-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 py-4 sm:px-6 bg-green-700">
                                <h2 class="text-sm font-medium leading-6 text-white">Head to head</h2>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="w-full max-w-full overflow-hidden">
                                    <div class="bg-gray-50">
                                        <div class="flex">
                                            <div scope="col"
                                                class="px-4 sm:px-6 py-2 text-left text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                #
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 sm:px-3 text-left text-sm font-semibold text-gray-900 w-6/12">
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
                                                            class="whitespace-nowrap py-3.5 px-4 sm:px-6 text-sm font-medium text-gray-900 text-left w-2/12 md:w-1/12">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                        <div
                                                            class="px-2 sm:px-3 py-3.5 text-sm font-medium text-gray-900 truncate w-6/12">
                                                            <span
                                                                class="{{ $team->shortname ? 'hidden md:inline' : '' }}">{{ $team->name }}</span>
                                                            @if ($team->shortname)
                                                                <span class="md:hidden"
                                                                    href="{{ route('team.show', $team->id) }}">{{ $team->shortname }}</span>
                                                            @endif
                                                        </div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-3.5 text-sm text-gray-500 font-semibold text-center w-2/12 md:w-1/12">
                                                            {{ $team->played }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-3.5 text-sm text-gray-500 font-semibold text-center hidden md:block w-1/12">
                                                            {{ $team->wins }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-3.5 text-sm text-gray-500 font-semibold text-center hidden md:block w-1/12">
                                                            {{ $team->draws }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-3.5 text-sm text-gray-500 font-semibold text-center hidden md:block w-1/12">
                                                            {{ $team->losses }}</div>
                                                        <div
                                                            class="whitespace-nowrap px-2 py-3.5 text-sm text-gray-500 font-semibold text-center w-2/12 md:w-1/12">
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
                                                    class="py-2 px-4 sm:px-6 text-left text-sm font-semibold text-gray-900 w-6/12 md:w-9/12">
                                                    Name</div>
                                                <div scope="col"
                                                    class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                    Pl</div>
                                                <div scope="col"
                                                    class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                    W</div>
                                                <div scope="col"
                                                    class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                    L</div>
                                            </div>
                                        </div>
                                        <div class="divide-y divide-gray-200 bg-white">
                                            @foreach ($fixture->homeTeam->players as $player)
                                                <a href="{{ route('player.show', $player->id) }}"
                                                    class=" hover:cursor-pointer hover:bg-gray-50 flex">
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-6/12 md:w-9/12 truncate">
                                                        {{ $player->name }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 md:w-1/12 text-center">
                                                        {{ $player->framesPlayed->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 md:w-1/12 text-center">
                                                        {{ $player->framesWon->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 md:w-1/12 text-center">
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
                                                    class="py-2 px-4 sm:px-6 text-left text-sm font-semibold text-gray-900 w-6/12 md:w-9/12">
                                                    Name</div>
                                                <div scope="col"
                                                    class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                    Pl</div>
                                                <div scope="col"
                                                    class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                    W</div>
                                                <div scope="col"
                                                    class="py-2 px-4 sm:px-6 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                    L</div>
                                            </div>
                                        </div>
                                        <div class="divide-y divide-gray-200 bg-white">
                                            @foreach ($fixture->awayTeam->players as $player)
                                                <a href="{{ route('player.show', $player->id) }}"
                                                    class=" hover:cursor-pointer hover:bg-gray-50 flex">
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-6/12 md:w-9/12 truncate">
                                                        {{ $player->name }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 md:w-1/12 text-center">
                                                        {{ $player->framesPlayed->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 md:w-1/12 text-center">
                                                        {{ $player->framesWon->count() }}
                                                    </div>
                                                    <div
                                                        class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-2/12 md:w-1/12 text-center">
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
