<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">{{ $ruleset->name }} Tables</h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
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
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">
                                                #
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 sm:px-3 text-left text-sm font-semibold text-gray-900 w-6/12">
                                                Team</div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">Pl
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:block w-1/12">W
                                            </div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:block w-1/12">D</div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:block w-1/12">L</div>
                                            <div scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 w-2/12 md:w-1/12">Pts</div>
                                        </tr>
                                    </div>
                                    <div class="bg-white">
                                        @foreach ($section->standings() as $team)
                                            <a href="{{ route('team.show', $team->id) }}" class="border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 flex">
                                                <div
                                                    class="whitespace-nowrap py-2 px-2 text-sm font-medium text-gray-900 text-center w-2/12 md:w-1/12">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div
                                                    class="px-2 sm:px-3 py-2 text-sm font-medium text-gray-900 truncate w-6/12">
                                                    <span class="{{ $team->shortname ? "hidden md:inline" : "" }}">{{ $team->name }}</span>
                                                    @if ($team->shortname)
                                                        <span class="md:hidden" href="{{ route('team.show', $team->id) }}">{{ $team->shortname }}</span>
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