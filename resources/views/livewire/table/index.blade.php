<div class="pt-[80px]">
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="border-b border-gray-200 pb-2 mb-4">
                <div class="-ml-2 -mt-2 flex flex-wrap items-baseline">
                  <h3 class="ml-2 mt-2 text-base font-semibold leading-6 text-gray-900">Tables</h3>
                  <p class="ml-2 mt-1 truncate text-sm text-gray-500">{{ $ruleset->name }}</p>
                </div>
            </div>                    
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-6">
                @foreach ($sections as $section)
                    <section>
                        <div class="bg-white shadow-md rounded-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 py-4 bg-green-700">
                                <h2 class="text-sm font-medium leading-6 text-white">{{ $section->name }}</h2>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="w-full max-w-full overflow-hidden">
                                    <div class="bg-gray-50">
                                        <div class="flex">
                                            <div class="flex w-6/12">
                                                <div scope="col"
                                                    class="px-4 py-2 text-left text-sm font-semibold text-gray-900 w-2/12">
                                                    #
                                                </div>
                                                <div scope="col"
                                                    class="px-2 py-2 sm:px-3 text-left text-sm font-semibold text-gray-900 w-10/12 truncate">
                                                    Team</div>
                                            </div>
                                            <div class="flex w-6/12">
                                                <div scope="col"
                                                    class="py-2 text-center text-sm font-semibold text-gray-900 w-1/5">Pl
                                                </div>
                                                <div scope="col"
                                                    class="py-2 text-center text-sm font-semibold text-gray-900 w-1/5">W
                                                </div>
                                                <div scope="col"
                                                    class="py-2 text-center text-sm font-semibold text-gray-900 w-1/5">D</div>
                                                <div scope="col"
                                                    class="py-2 text-center text-sm font-semibold text-gray-900 w-1/5">L</div>
                                                <div scope="col"
                                                    class="py-2 text-center text-sm font-semibold text-gray-900 w-1/5">Pts</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white">
                                        @foreach ($section->standings() as $team)
                                            <a href="{{ route('team.show', $team->id) }}" class="border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 flex">
                                                <div class="flex w-6/12">
                                                    <div
                                                        class="whitespace-nowrap py-2 px-4 text-sm font-medium text-gray-900 text-left w-2/12">
                                                        {{$loop->iteration}}.
                                                    </div>
                                                    <div
                                                        class="tooltip tooltip-top px-2 sm:px-3 py-2 text-sm text-gray-900 w-10/12 truncate {{ $team->pivot->withdrawn_at ? 'line-through' : '' }}">
                                                        <span class="{{ $team->shortname ? "hidden md:inline" : "" }}">{{ $team->name }}</span>
                                                        @if ($team->shortname)
                                                            <span class="md:hidden" href="{{ route('team.show', $team->id) }}">{{ $team->shortname }}</span>
                                                        @endif
                                                        @if ($team->pivot->withdrawn_at)
                                                        <span class="tooltip-text">
                                                            This team was withdrawn from the section on {{ date('d/m/Y', strtotime($team->pivot->withdrawn_at)) }}
                                                        </span>
                                                        @endif        
                                                    </div>
                                                </div>
                                                <div class="flex w-6/12">
                                                    <div
                                                        class="whitespace-nowrap py-2 text-sm text-gray-900 font-semibold text-center w-1/5">
                                                        {{$team->played}}</div>
                                                    <div
                                                        class="whitespace-nowrap py-2 text-sm text-gray-900 font-semibold text-center w-1/5">
                                                        {{$team->wins}}</div>
                                                    <div
                                                        class="whitespace-nowrap py-2 text-sm text-gray-900 font-semibold text-center w-1/5">
                                                        {{$team->draws}}</div>
                                                    <div
                                                        class="whitespace-nowrap py-2 text-sm text-gray-900 font-semibold text-center w-1/5">
                                                        {{$team->losses}}</div>
                                                    <div
                                                        class="whitespace-nowrap py-2 text-sm text-gray-900 font-semibold text-center w-1/5">
                                                        {{$team->points}}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>