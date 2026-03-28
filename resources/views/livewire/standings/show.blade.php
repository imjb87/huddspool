<section data-section-table-view class="mt-0">
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
                        <div class="flex items-center justify-between gap-2 pb-0.5 sm:-mx-3 sm:px-3" data-section-table-band>
                            <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                            <div class="ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-3">
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">Pl</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">W</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">D</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">L</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">Pts</div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                            @foreach ($standingRows as $row)
                                @if ($row->can_link)
                                    <a class="group block {{ $row->withdrawn ? 'line-through' : '' }}"
                                        wire:key="section-standing-{{ $section->id }}-{{ $row->id }}"
                                        data-section-table-row-type="link"
                                        href="{{ route('team.show', $row->id) }}">
                                @else
                                    <div class="block {{ $row->withdrawn ? 'line-through' : '' }}"
                                        wire:key="section-standing-{{ $section->id }}-{{ $row->id }}"
                                        data-section-table-row-type="static">
                                @endif
                                    <div class="flex items-center justify-between gap-2 rounded-lg py-2 sm:-mx-3 sm:-my-px sm:gap-3 sm:px-3 sm:py-3 sm:transition sm:group-hover:bg-gray-200/70 dark:sm:group-hover:bg-zinc-800/70" data-section-table-band>
                                        <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3">
                                            <div class="w-4 shrink-0 text-center text-sm font-semibold tabular-nums text-gray-500 dark:text-gray-400 sm:w-7">
                                                {{ $row->position }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate whitespace-nowrap text-sm font-semibold {{ $row->text_class }}">
                                                    <span class="{{ $row->shortname ? 'hidden md:inline' : '' }}">
                                                        {{ $row->name }}
                                                    </span>
                                                    @if ($row->shortname)
                                                        <span class="md:hidden whitespace-nowrap {{ $row->text_class }}">
                                                            {{ $row->shortname }}
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-3">
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->played }}</p>
                                            </div>
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->wins }}</p>
                                            </div>
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->draws }}</p>
                                            </div>
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->losses }}</p>
                                            </div>
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->points_class }}">{{ $row->points }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @if ($row->can_link)
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
