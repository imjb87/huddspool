<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                    <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Player profile</h3>
                </div>
            </div>
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="overflow-hidden bg-white shadow rounded-lg sm:self-start w-full lg:w-1/3">
                    <div class="md:flex md:items-center md:justify-between md:space-x-5 px-4 py-6 sm:px-6">
                        <div class="flex items-start space-x-5">
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <img class="h-16 w-16 rounded-full" src="{{ asset('/images/user.jpeg') }}"
                                        alt="">
                                    <span class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></span>
                                </div>
                            </div>
                            <!--
                            Use vertical padding to simulate center alignment when both lines of text are one line,
                            but preserve the same layout if the text wraps without making the image jump around.
                          -->
                            <div class="pt-1.5">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $player->name }}</h1>
                                <p class="text-sm font-medium text-gray-500">
                                    @if ($player->team)
                                        <a href="{{ route('team.show', $player->team) }}">
                                            {{ $player->team->name }}
                                        </a>
                                    @else
                                        Free agent
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100">
                        <dl class="divide-y divide-gray-100">
                            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium leading-6 text-gray-900">Role</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                    {{ $role }}</dd>
                            </div>
                            @if ($player->email)
                                <a href="mailto:{{ $player->email }}"
                                    class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Email address</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $player->email }}
                                    </dd>
                                </a>
                            @endif
                            @if ($player->telephone)
                                <a href="tel:{{ $player->telephone }}"
                                    class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Telephone</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $player->telephone }}
                                    </dd>
                                </a>
                            @endif
                        </dl>
                    </div>
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
                                                href="{{ route('result.show', $frame->result) }}">
                                                <div
                                                    class="whitespace-nowrap px-4 py-4 text-sm flex items-center w-full">
                                                    <div class="pr-4">
                                                        @if ($frame->home_player_id == $player->id)
                                                            @if ($frame->home_score > $frame->away_score)
                                                                <span
                                                                    class="inline-block bg-green-700 text-white text-center mx-auto text-xs leading-5 min-w-[22px] font-extrabold">W</span>
                                                            @else
                                                                <span
                                                                    class="inline-block bg-red-700 text-white text-center mx-auto text-xs leading-5 min-w-[22px] font-extrabold">L</span>
                                                            @endif
                                                        @else
                                                            @if ($frame->home_score < $frame->away_score)
                                                                <span
                                                                    class="inline-block bg-green-700 text-white text-center mx-auto text-xs leading-5 min-w-[22px] font-extrabold">W</span>
                                                            @else
                                                                <span
                                                                    class="inline-block bg-red-700 text-white text-center mx-auto text-xs leading-5 min-w-[22px] font-extrabold">L</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-col flex-grow">
                                                        <div>
                                                            {{ $frame->home_player_id == $player->id ? $frame->awayPlayer->name : $frame->homePlayer->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $frame->home_player_id == $player->id ? $frame->awayPlayer->team->name : $frame->homePlayer->team->name }}
                                                        </div>
                                                    </div>
                                                    <div class="w-2/12 text-right text-gray-500 font-semibold">
                                                        {{ $frame->result->fixture->fixture_date->format('d/m') }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ( $history->count() > 0 )
                        <div class="bg-white shadow rounded-lg mt-6 overflow-hidden">
                            <div class="px-4 py-4 bg-green-700">
                                <h2 class="text-sm font-medium leading-6 text-white">History</h2>
                            </div>
                            <div class="border-gray-200 h-full flex flex-col">
                                <div class="min-w-full overflow-hidden">
                                    <div class="bg-white overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="py-2 px-4 text-left text-sm font-semibold text-gray-900">
                                                        Season
                                                    </th>
                                                    <th scope="col"
                                                        class="py-2 px-4 text-left text-sm font-semibold text-gray-900">
                                                        Team
                                                    </th>
                                                    <th scope="col"
                                                        class="py-2 px-4 text-center text-sm font-semibold text-gray-900">
                                                        Played
                                                    </th>
                                                    <th scope="col"
                                                        class="py-2 px-4 text-center text-sm font-semibold text-gray-900">
                                                        Won
                                                    </th>
                                                    <th scope="col"
                                                        class="py-2 px-4 text-center text-sm font-semibold text-gray-900">
                                                        Lost
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($history as $season)
                                                    <tr>
                                                        <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-gray-900">
                                                            {{ $season->season }}
                                                        </td>
                                                        <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-gray-900">
                                                            {{ $season->team }}
                                                        </td>
                                                        <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-gray-900 text-center">
                                                            {{ $season->played }}
                                                        </td>
                                                        <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-gray-900 text-center">
                                                            {{ $season->won }}
                                                        </td>
                                                        <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-gray-900 text-center">
                                                            {{ $season->lost }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>
