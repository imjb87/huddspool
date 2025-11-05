<section>
    <div class="bg-white shadow sm:rounded-lg flex flex-col h-full overflow-hidden -mx-4 sm:mx-0 grow-0">
        <div class="px-4 py-4 sm:px-6 bg-green-700 flex items-center justify-between">
            <h2 class="text-sm font-medium leading-6 text-white">Standings</h2>
            <span class="text-xs font-semibold uppercase tracking-wide text-green-100">{{ $section->name }}</span>
        </div>
        <div class="border-t border-gray-200 h-full flex flex-col">
            <div class="min-w-full overflow-hidden">
                <div class="bg-gray-50 flex">
                    <div class="flex w-1/2 pl-4 sm:pl-6">
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-2/12">#</div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-10/12">Team</div>
                    </div>
                    <div class="flex w-1/2">
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">Pl</div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">W</div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">D</div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">L</div>
                        <div scope="col" class="py-2 text-sm font-semibold text-gray-900 w-1/5 text-center">Pts</div>
                    </div>
                </div>
                <div class="bg-white">
                    @if ($standings->isEmpty())
                        <div class="text-center m-4 p-4 rounded-lg border-2 border-dashed border-gray-300">
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No standings available for this section yet.</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-prose mx-auto">
                                Standings will appear once results are entered for this section.
                            </p>
                        </div>
                    @else
                        @foreach ($standings as $index => $team)
                            @php
                                $withdrawn = (bool) ($team->pivot->withdrawn_at ?? false);
                                $textClass = $withdrawn ? 'text-gray-400' : 'text-gray-900';
                            @endphp
                            <a class="flex w-full border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50 {{ $withdrawn ? 'line-through' : '' }}"
                                href="{{ route('team.show', $team->id) }}">
                                <div class="flex w-1/2 pl-4 sm:pl-6 items-center">
                                    <div class="whitespace-nowrap py-2 text-sm {{ $textClass }} w-2/12 font-semibold">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="whitespace-nowrap py-2 text-sm {{ $textClass }} w-10/12 flex flex-col">
                                        <span class="{{ $team->shortname ? 'hidden md:inline' : '' }}">
                                            {{ $team->name }}
                                        </span>
                                        @if ($team->shortname)
                                            <span class="md:hidden {{ $textClass }}">
                                                {{ $team->shortname }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex w-1/2 items-center">
                                    <div class="py-2 text-sm {{ $textClass }} w-1/5 text-center">
                                        {{ $team->played }}
                                    </div>
                                    <div class="py-2 text-sm {{ $textClass }} w-1/5 text-center">
                                        {{ $team->wins }}
                                    </div>
                                    <div class="py-2 text-sm {{ $textClass }} w-1/5 text-center">
                                        {{ $team->draws }}
                                    </div>
                                    <div class="py-2 text-sm {{ $textClass }} w-1/5 text-center">
                                        {{ $team->losses }}
                                    </div>
                                    <div class="py-2 text-sm w-1/5 text-center font-semibold {{ $withdrawn ? 'text-gray-400' : 'text-green-700' }}">
                                        {{ $team->points }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
