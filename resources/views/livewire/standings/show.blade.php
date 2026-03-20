<section data-section-table-view class="mt-0">
    @php
        $isHistoryView = $history ?? false;
        $summaryCopy = $isHistoryView
            ? 'Archived positions, results and points for this section.'
            : 'Current positions, results and points for this section.';
    @endphp
    <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Standings</h2>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $summaryCopy }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div data-section-table-shell>
                    @if ($standings->isEmpty())
                        <div class="px-4 py-10 text-center sm:px-6">
                            <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No standings available for this section yet.</h3>
                                <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                    Standings will appear once results are entered for this section.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between gap-2 pb-2" data-section-table-band>
                            <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                            <div class="ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-4">
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-12">Pl</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-12">W</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-12">D</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-12">L</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-12">Pts</div>
                            </div>
                        </div>

                        @foreach ($standings as $index => $team)
                            @php
                                $withdrawn = (bool) ($team->pivot->withdrawn_at ?? false);
                                $textClass = $withdrawn ? 'text-gray-400 dark:text-zinc-500' : 'text-gray-900 dark:text-gray-100';
                                $pointsClass = $withdrawn ? 'text-gray-400 dark:text-zinc-500' : 'text-green-700 dark:text-green-400';
                                $displayName = $isHistoryView ? ($team->archived_name ?? $team->name) : $team->name;
                            @endphp
                            @php
                                $canLinkTeam = ! ($isHistoryView && ($team->trashed ?? false));
                            @endphp
                            @if ($canLinkTeam)
                                <a class="block rounded-lg py-3 {{ $withdrawn ? 'line-through' : '' }}"
                                    wire:key="section-standing-{{ $section->id }}-{{ $team->id }}"
                                    data-section-table-row-type="link"
                                    href="{{ route('team.show', $team->id) }}">
                            @else
                                <div class="block rounded-lg py-3 {{ $withdrawn ? 'line-through' : '' }}"
                                    wire:key="section-standing-{{ $section->id }}-{{ $team->id }}"
                                    data-section-table-row-type="static">
                            @endif
                                <div class="flex items-center justify-between gap-2 sm:gap-3" data-section-table-band>
                                    <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3">
                                        <div class="w-4 shrink-0 text-sm font-semibold tabular-nums {{ $textClass }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate whitespace-nowrap text-sm font-semibold {{ $textClass }}">
                                                <span class="{{ $team->shortname ? 'hidden md:inline' : '' }}">
                                                    {{ $displayName }}
                                                </span>
                                                @if ($team->shortname)
                                                    <span class="md:hidden whitespace-nowrap {{ $textClass }}">
                                                        {{ $isHistoryView ? $displayName : $team->shortname }}
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-4">
                                        <div class="w-8 sm:w-12">
                                            <p class="text-sm font-semibold {{ $textClass }}">{{ $team->played }}</p>
                                        </div>
                                        <div class="w-8 sm:w-12">
                                            <p class="text-sm font-semibold {{ $textClass }}">{{ $team->wins }}</p>
                                        </div>
                                        <div class="w-8 sm:w-12">
                                            <p class="text-sm font-semibold {{ $textClass }}">{{ $team->draws }}</p>
                                        </div>
                                        <div class="w-8 sm:w-12">
                                            <p class="text-sm font-semibold {{ $textClass }}">{{ $team->losses }}</p>
                                        </div>
                                        <div class="w-8 sm:w-12">
                                            <p class="text-sm font-semibold {{ $pointsClass }}">{{ $team->points }}</p>
                                        </div>
                                    </div>
                                </div>
                            @if ($canLinkTeam)
                                </a>
                            @else
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
