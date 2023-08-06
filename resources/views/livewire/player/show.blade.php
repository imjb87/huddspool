<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">{{ $player->name }}
                </h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5 w-full lg:w-1/3 self-start">
                    <dl class="flex flex-wrap pb-6">
                        <div class="flex-auto pl-6 pt-6">
                            <dt class="text-sm font-semibold leading-6 text-gray-900">Team</dt>
                            <dd class="mt-1 text-base font-semibold leading-6 text-gray-900">
                                <a href="{{ route('team.show', $player->team->id) }}"
                                    class="hover:underline">{{ $player->team->name }}</a>
                            </dd>
                        </div>
                        <div class="mt-6 flex w-full flex-none gap-x-4 border-t border-gray-900/5 px-6 pt-6">
                            <dt class="flex w-5">
                                <span class="sr-only">Player email address</span>
                                <svg class="w-4 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 512 512" fill="currentColor">
                                    <!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208c13.3 0 24 10.7 24 24s-10.7 24-24 24C114.6 512 0 397.4 0 256S114.6 0 256 0S512 114.6 512 256v28c0 50.8-41.2 92-92 92c-31.1 0-58.7-15.5-75.3-39.2C322.7 360.9 291.1 376 256 376c-66.3 0-120-53.7-120-120s53.7-120 120-120c28.8 0 55.2 10.1 75.8 27c4.3-6.6 11.7-11 20.2-11c13.3 0 24 10.7 24 24v80 28c0 24.3 19.7 44 44 44s44-19.7 44-44V256c0-114.9-93.1-208-208-208zm72 208a72 72 0 1 0 -144 0 72 72 0 1 0 144 0z" />
                                </svg>
                            </dt>
                            <dd class="text-sm leading-6 text-gray-900">
                                <a href="mailto:{{ $player->email }}"
                                    class="text-sm font-medium leading-6 text-gray-900 hover:underline">{{ $player->email }}</a>
                            </dd>
                        </div>
                        @if ($player->telephone)
                            <div class="mt-4 flex w-full flex-none gap-x-4 px-6">
                                <dt class="flex w-5">
                                    <span class="sr-only">Player telephone</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 text-gray-400 mx-auto"
                                        viewBox="0 0 512 512" fill="currentColor">
                                        <path
                                            d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
                                    </svg>
                                </dt>
                                <dd class="text-sm leading-6 text-gray-900">
                                    <a href="tel:{{ $player->telephone }}"
                                        class="text-sm font-medium leading-6 text-gray-900 hover:underline">{{ $player->telephone }}</a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
                <section class="w-full lg:w-2/3">
                    <dl
                        class="mb-5 grid grid-cols-3 divide-gray-200 overflow-hidden rounded-lg bg-white shadow md:grid-cols-3 divide-x">
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-base font-normal text-gray-900">Played</dt>
                            <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                <div class="flex items-baseline text-2xl font-semibold text-green-700">
                                    {{ $played ?? 0 }}
                                </div>
                            </dd>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-base font-normal text-gray-900">Won</dt>
                            <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                <div class="flex items-baseline text-2xl font-semibold text-green-700">
                                    {{ $won ?? 0 }}
                                    <span class="ml-2 text-sm font-medium text-gray-500">
                                        ({{ $played > 0 ? round(($won / $played) * 100) : 0 }}%)
                                    </span>
                                </div>
                            </dd>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dt class="text-base font-normal text-gray-900">Lost</dt>
                            <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                                <div class="flex items-baseline text-2xl font-semibold text-green-700">
                                    {{ $lost ?? 0 }}
                                    <span class="ml-2 text-sm font-medium text-gray-500">
                                        ({{ $played > 0 ? round(($lost / $played) * 100) : 0 }}%)
                                    </span>
                                </div>
                            </dd>
                        </div>
                    </dl>

                    <div class="bg-white shadow rounded-lg flex flex-col overflow-hidden">
                        <div class="px-4 py-4 bg-green-700">
                            <h2 class="text-sm font-medium leading-6 text-white">Frames</h2>
                        </div>
                        <div class="border-gray-200 h-full flex flex-col">
                            <div class="min-w-full overflow-hidden">
                                <div class="bg-white">
                                    @if (count($frames) == 0)
                                        <div
                                            class="text-center m-4 p-4 rounded-lg border-2 border-dashed border-gray-300">
                                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No frames</h3>
                                            <p class="mt-1 text-sm text-gray-500 max-w-prose mx-auto">This player hasn't
                                                played any frames this season. Please check back here again soon.</p>
                                        </div>
                                    @else
                                        @foreach ($frames as $frame)
                                            <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                                                href="{{ route('fixture.show', $frame->result->fixture) }}">
                                                <div class="whitespace-nowrap px-4 sm:px-6 py-4 text-sm text-gray-500">
                                                    @if ($frame->home_player_id == $player->id)
                                                        @if ($frame->home_score > $frame->away_score)
                                                            <span
                                                                class="inline-block bg-green-700 text-white rounded-md w-6 text-center text-xs font-semibold mr-2">W</span>
                                                        @else
                                                            <span
                                                                class="inline-block bg-red-700 text-white rounded-md w-6 text-center text-xs font-semibold mr-2">L</span>
                                                        @endif
                                                    @else
                                                        @if ($frame->home_score < $frame->away_score)
                                                            <span
                                                                class="inline-block bg-green-700 text-white rounded-md w-6 text-center text-xs font-semibold mr-2">W</span>
                                                        @else
                                                            <span
                                                                class="inline-block bg-red-700 text-white rounded-md w-6 text-center text-xs font-semibold mr-2">L</span>
                                                        @endif
                                                    @endif
                                                    vs
                                                    {{ $frame->home_player_id == $player->id ? $frame->awayPlayer->name : $frame->homePlayer->name }}
                                                    <span class="hidden md:inline">
                                                        ({{ $frame->home_player_id == $player->id ? $frame->awayPlayer->team->name : $frame->homePlayer->team->name }})
                                                    </span>
                                                    <span class="md:hidden">
                                                        ({{ $frame->home_player_id == $player->id ? ( $frame->awayPlayer->team->shortname ?? $frame->awayPlayer->team->name ) : ( $frame->homePlayer->team->shortname ?? $frame->homePlayer->team->name ) }})
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $frame->result->fixture->fixture_date->format('d/m') }}
                                                    </span>
                                                </div>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>
