<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">{{ $team->name }}
                </h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="w-full lg:w-1/3 self-start flex flex-col gap-y-6">
                    <dl class="bg-white flex flex-wrap rounded-lg shadow-sm ring-1 ring-gray-900/5">
                        <div class="flex-auto pl-6 pt-6">
                            <dt class="text-sm font-semibold leading-6 text-gray-900">Current section</dt>
                            <dd class="mt-1 text-base font-semibold leading-6 text-gray-900">
                                <a class="hover:underline"
                                    href="{{ route('table.index', $team->section()->ruleset->id) }}">
                                    {{ $team->section()->name }}
                                </a>
                            </dd>
                        </div>
                        <div class="mt-6 flex w-full flex-none gap-x-4 border-t border-gray-900/5 px-6 pt-6">
                            <dt class="flex-none">
                                <span class="sr-only">Captain name</span>
                                <svg class="h-6 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z"
                                        clip-rule="evenodd" />
                                </svg>
                            </dt>
                            <dd class="text-sm font-medium leading-6 text-gray-900">
                                <a class="hover:underline"
                                    href="{{ route('player.show', $team->captain->id ?? 0) }}">{{ $team->captain?->name }}</a>
                            </dd>
                        </div>
                        @if ($team->captain?->telephone)
                            <div class="mt-4 flex w-full flex-none gap-x-4 px-6">
                                <dt class="flex w-5">
                                    <span class="sr-only">Captain telephone</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 text-gray-400 mx-auto"
                                        viewBox="0 0 512 512" fill="currentColor">
                                        <path
                                            d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
                                    </svg>
                                </dt>
                                <dd class="text-sm leading-6 text-gray-900">
                                    <a href="tel:{{ $team->captain?->telephone }}"
                                        class="text-sm font-medium leading-6 text-gray-900 hover:underline">{{ $team->captain?->telephone }}</a>
                                </dd>
                            </div>
                        @endif
                        <div class="mt-4 flex w-full flex-none gap-x-4 px-6">
                            <dt class="flex w-5">
                                <span class="sr-only">Captain email address</span>
                                <svg class="w-4 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 512 512" fill="currentColor">
                                    <!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208c13.3 0 24 10.7 24 24s-10.7 24-24 24C114.6 512 0 397.4 0 256S114.6 0 256 0S512 114.6 512 256v28c0 50.8-41.2 92-92 92c-31.1 0-58.7-15.5-75.3-39.2C322.7 360.9 291.1 376 256 376c-66.3 0-120-53.7-120-120s53.7-120 120-120c28.8 0 55.2 10.1 75.8 27c4.3-6.6 11.7-11 20.2-11c13.3 0 24 10.7 24 24v80 28c0 24.3 19.7 44 44 44s44-19.7 44-44V256c0-114.9-93.1-208-208-208zm72 208a72 72 0 1 0 -144 0 72 72 0 1 0 144 0z" />
                                </svg>
                            </dt>
                            <dd class="text-sm leading-6 text-gray-900">
                                <a href="mailto:{{ $team->captain?->email }}"
                                    class="text-sm font-medium leading-6 text-gray-900 hover:underline">{{ $team->captain?->email }}</a>
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
                                    href="{{ route('venue.show', $team->venue->id) }}">
                                    {{ $team->venue->name }}
                                </a>
                            </dd>
                        </div>
                    </dl>
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
                                        @foreach ($team->players as $player)
                                            <a href="{{ route('player.show', $player->id) }}"
                                                class=" hover:cursor-pointer hover:bg-gray-50 flex">
                                                <div
                                                    class="block whitespace-nowrap py-4 px-4 sm:px-6 text-sm font-medium text-gray-900 w-6/12 truncate">
                                                    {{ $player->name }}
                                                    @if ($player->role == 2)
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-green-700 px-1.5 py-0.5 text-xs font-semibold text-white align-top">T</span>
                                                    @endif
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
                                    @foreach ($team->fixtures->sortBy('fixture_date') as $fixture)
                                        <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                                            href="{{ route('fixture.show', $fixture) }}">
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-right w-5/12 {{ $fixture->homeTeam->shortname ? 'hidden md:block' : '' }}">
                                                {{ $fixture->homeTeam->name }}</div>
                                            @if ($fixture->homeTeam->shortname)
                                                <div
                                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-right w-5/12 md:hidden">
                                                    {{ $fixture->homeTeam->shortname }}</div>
                                            @endif
                                            @if ($fixture->result)
                                                <div
                                                    class="whitespace-nowrap px-1 py-3 text-sm text-gray-500 text-right font-semibold w-2/12">
                                                    <div class="block bg-green-700 text-white text-center p-1">
                                                        {{ $fixture->result->home_score ?? '' }} -
                                                        {{ $fixture->result->away_score ?? '' }}</div>
                                                </div>
                                            @else
                                                <div
                                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-center font-semibold w-2/12">
                                                    {{ $fixture->fixture_date->format('d/m') }}
                                                </div>
                                            @endif
                                            <div
                                                class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-left w-5/12 {{ $fixture->awayTeam->shortname ? 'hidden md:block' : '' }}">
                                                {{ $fixture->awayTeam->name }}</div>
                                            @if ($fixture->awayTeam->shortname)
                                                <div
                                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-left w-5/12 md:hidden">
                                                    {{ $fixture->awayTeam->shortname }}</div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
