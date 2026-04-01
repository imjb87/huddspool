<section data-section-table-view class="ui-section">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="ui-section-intro">
                    <div class="ui-section-intro-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>

                    <div class="ui-section-intro-copy">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Standings</h2>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ $summaryCopy }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card" data-section-table-shell>
                    @if ($standings->isEmpty())
                        <div class="ui-card-body py-10 text-center">
                            <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-neutral-800 dark:bg-neutral-900/75">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No standings available for this section yet.</h3>
                                <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                    Standings will appear once results are entered for this section.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="ui-card-column-headings px-4 sm:px-5" data-section-table-band>
                            <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                            <div class="ml-auto grid shrink-0 grid-cols-4 gap-2 text-center sm:grid-cols-5 sm:gap-3">
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">Pl</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">W</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">D</div>
                                <div class="hidden w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:block sm:w-10">L</div>
                                <div class="w-8 text-xs font-medium text-gray-500 dark:text-gray-400 sm:w-10">Pts</div>
                            </div>
                        </div>

                        @php
                            $standingCount = $standingRows->count();
                        @endphp

                        <div class="ui-card-rows">
                            @foreach ($standingRows as $row)
                                @php
                                    $rowAccentClass = match (true) {
                                        $loop->iteration <= 2 => 'bg-emerald-500 dark:bg-emerald-400',
                                        $loop->iteration > 2 && ($standingCount - $loop->iteration) < 2 => 'bg-rose-500 dark:bg-rose-400',
                                        default => null,
                                    };
                                @endphp
                                @if ($row->can_link)
                                    <a class="ui-card-row-link group {{ $row->withdrawn ? 'line-through' : '' }}"
                                        wire:key="section-standing-{{ $section->id }}-{{ $row->id }}"
                                        data-section-table-row-type="link"
                                        href="{{ route('team.show', $row->id) }}">
                                @else
                                    <div class="{{ $row->withdrawn ? 'line-through' : '' }}"
                                        wire:key="section-standing-{{ $section->id }}-{{ $row->id }}"
                                        data-section-table-row-type="static">
                                @endif
                                    <div class="ui-card-row relative gap-2 px-4 sm:gap-3 sm:px-5" data-section-table-band>
                                        @if ($rowAccentClass)
                                            <span
                                                aria-hidden="true"
                                                class="absolute inset-y-2 left-0 w-1 rounded-r-full {{ $rowAccentClass }}"
                                            ></span>
                                        @endif

                                        <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3">
                                            <div class="w-5 shrink-0 text-center text-sm font-semibold tabular-nums text-gray-500 dark:text-gray-400 sm:w-7">
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

                                        <div class="ml-auto grid shrink-0 grid-cols-4 gap-2 text-center sm:grid-cols-5 sm:gap-3">
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->played }}</p>
                                            </div>
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->wins }}</p>
                                            </div>
                                            <div class="w-8 sm:w-10">
                                                <p class="text-sm font-semibold {{ $row->text_class }}">{{ $row->draws }}</p>
                                            </div>
                                            <div class="hidden w-8 sm:block sm:w-10">
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
