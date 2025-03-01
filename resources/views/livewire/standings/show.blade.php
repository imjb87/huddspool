<section class="-mx-4 sm:m-0">
    <div class="bg-white shadow-md sm:rounded-lg overflow-hidden">
        <div class="px-4 py-4 bg-green-700">
            <h2 class="text-sm font-medium leading-6 text-white">{{ $section->name }}</h2>
        </div>
        <div class="border-t border-gray-200">
            <div class="w-full max-w-full overflow-hidden">
                <div class="bg-gray-50">
                    <div class="flex">
                        <div class="flex w-6/12 pl-4">
                            <div scope="col"
                                class="py-2 text-left text-sm font-semibold text-gray-900 w-2/12">
                                #
                            </div>
                            <div scope="col"
                                class="py-2 text-left text-sm font-semibold text-gray-900 w-10/12 truncate">
                                Team</div>
                        </div>
                        <div class="flex w-6/12 pr-2">
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
                    @foreach ($standings as $team)
                        <a href="{{ route('team.show', $team->id) }}" class="border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 flex">
                            <div class="flex w-6/12 pl-4">
                                <div
                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-left font-semibold w-2/12">
                                    {{$loop->iteration}}.
                                </div>
                                <div
                                    class="tooltip tooltip-top py-2 text-sm text-gray-900 w-10/12 truncate {{ $team->pivot->withdrawn_at ? 'line-through' : '' }}">
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
                            <div class="flex w-6/12 pr-2">
                                <div
                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-center w-1/5">
                                    {{$team->pivot->played}}</div>
                                <div
                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-center w-1/5">
                                    {{$team->pivot->wins}}</div>
                                <div
                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-center w-1/5">
                                    {{$team->pivot->draws}}</div>
                                <div
                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-center w-1/5">
                                    {{$team->pivot->losses}}</div>
                                <div
                                    class="whitespace-nowrap py-2 text-sm text-gray-900 text-center w-1/5">
                                    {{$team->pivot->points}}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>