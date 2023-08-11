<div class="mt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                    <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Result</h3>
                    <p class="ml-2 mt-1 truncate text-sm text-gray-500">{{ $result->fixture->section->name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-6">
                <div class="w-full lg:w-1/3">
                    <div class="overflow-hidden bg-white shadow rounded-lg">
                        <div class="md:flex md:items-center md:justify-between md:space-x-5 px-4 py-6 sm:px-6">
                            <div class="flex items-start space-x-5">
                                <div class="pt-1.5">
                                    <h1 class="text-base font-semibold leading-6 text-gray-900">
                                        {{ $result->home_team_name }} vs {{ $result->away_team_name }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-100">
                            <dl class="divide-y divide-gray-100">
                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Date</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        <date>{{ $result->fixture->fixture_date->format('l jS F Y') }}</date>
                                    </dd>
                                </div>
                                <a href="{{ route('ruleset.show', $result->fixture->section->ruleset) }}"
                                    class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Ruleset</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $result->fixture->section->ruleset->name }}</dd>
                                </a>
                                <a href="{{ route('venue.show', $result->fixture->venue) }}"
                                    class="block px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium leading-6 text-gray-900">Venue</dt>
                                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                        {{ $result->fixture->venue->name }}</dd>
                                </a>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-2/3 flex flex-col gap-y-6">
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
                            @foreach ($result->frames as $key => $frame)
                                <div class="flex flex-wrap bg-white">
                                    <div
                                        class="w-full sm:w-auto flex sm:flex-1 order-2 sm:order-first border-b border-t border-gray-200 sm:border-0">
                                        <a href="{{ route('player.show', $frame->homePlayer) }}"
                                            class="border-0 py-2 px-4 sm:px-6 leading-6 text-sm flex-1 focus:outline-0 focus:ring-0">
                                            {{ $frame->homePlayer->name ?? 'Awarded' }}
                                        </a>
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
                                        <div class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200">
                                            <div
                                                class="block w-full border-0 pr-0 pl-0 py-2 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0">
                                                {{ $frame->away_score }}
                                            </div>
                                        </div>
                                        <a href="{{ route('player.show', $frame->awayPlayer) }}"
                                            class="border-0 py-2 px-4 sm:px-6 leading-6 text-sm flex-1 order-first sm:order-last focus:outline-0 focus:ring-0">
                                            {{ $frame->awayPlayer->name ?? 'Awarded' }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex flex-wrap bg-gray-50 font-semibold text-gray-900 text-sm">
                                <div class="w-full sm:w-auto flex sm:flex-1 border-b border-gray-200">
                                    <div class="flex-1 leading-6 py-2 px-4 sm:px-6 sm:text-right">
                                        Home total
                                    </div>
                                    <div class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200">
                                        {{ $result->home_score }}
                                    </div>
                                </div>
                                <div class="w-10 sm:w-12 bg-gray-50"></div>
                                <div class="w-full sm:w-auto flex sm:flex-1">
                                    <div
                                        class="w-10 sm:w-12 leading-6 py-2 text-center border-x border-gray-200 order-last sm:order-first">
                                        {{ $result->away_score }}
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
                                {{ $result->submittedBy->name }} on
                                {{ $result->created_at->format('l jS F Y') }} at
                                {{ $result->created_at->format('H:i') }}.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>
