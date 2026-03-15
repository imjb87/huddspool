<section data-history-standings-view>
    <div class="grow-0 overflow-hidden bg-white shadow-sm sm:rounded-lg -mx-4 flex h-full flex-col sm:mx-0">
        <div class="flex items-center justify-between bg-green-700 px-4 py-4 sm:px-6">
            <h2 class="text-sm font-medium leading-6 text-white">Standings</h2>
            <span class="text-xs font-semibold uppercase tracking-wide text-green-100">{{ $section->name }}</span>
        </div>
        <div class="flex h-full flex-col border-t border-gray-200">
            <div class="min-w-full overflow-hidden">
                <div class="flex bg-gray-50">
                    <div class="flex w-1/2 pl-4 sm:pl-6">
                        <div scope="col" class="w-2/12 py-2 text-sm font-semibold text-gray-900">#</div>
                        <div scope="col" class="w-10/12 py-2 text-sm font-semibold text-gray-900">Team</div>
                    </div>
                    <div class="flex w-1/2">
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900">Pl</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900">W</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900">D</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900">L</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900">Pts</div>
                    </div>
                </div>
                <div class="bg-white">
                    @if ($standings->isEmpty())
                        <div class="m-4 rounded-lg border-2 border-dashed border-gray-300 p-4 text-center">
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No standings available for this section yet.</h3>
                            <p class="mx-auto mt-1 max-w-prose text-sm text-gray-500">
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
                                <div class="flex w-1/2 items-center pl-4 sm:pl-6">
                                    <div class="w-2/12 whitespace-nowrap py-2 text-sm font-semibold {{ $textClass }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex w-10/12 flex-col whitespace-nowrap py-2 text-sm {{ $textClass }}">
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
                                    <div class="w-1/5 py-2 text-center text-sm {{ $textClass }}">
                                        {{ $team->played }}
                                    </div>
                                    <div class="w-1/5 py-2 text-center text-sm {{ $textClass }}">
                                        {{ $team->wins }}
                                    </div>
                                    <div class="w-1/5 py-2 text-center text-sm {{ $textClass }}">
                                        {{ $team->draws }}
                                    </div>
                                    <div class="w-1/5 py-2 text-center text-sm {{ $textClass }}">
                                        {{ $team->losses }}
                                    </div>
                                    <div class="w-1/5 py-2 text-center text-sm font-semibold {{ $withdrawn ? 'text-gray-400' : 'text-green-700' }}">
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
