<section data-section-table-view class="ui-section">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-numbers size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M11 6h9" />
                            <path d="M11 12h9" />
                            <path d="M12 18h8" />
                            <path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" />
                            <path d="M6 10v-6l-2 2" />
                        </svg>
                    </span>

                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Standings</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            {{ $summaryCopy }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card ui-standings-card" data-section-table-shell>
                    @if ($standings->isEmpty())
                        <x-ui-empty-state
                            title="No standings available for this section yet."
                            description="The table will populate after the first confirmed result is entered."
                            data-section-table-empty
                        />
                    @else
                        @php
                            $standingCount = $standingRows->count();
                        @endphp

                        <div class="ui-standings-item-group" data-slot="item-group">
                            <div class="ui-standings-item ui-standings-item-header" data-section-table-band data-slot="item" data-variant="muted" data-size="default">
                                <div class="ui-standings-item-content" data-slot="item-content">
                                    <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3"></div>

                                    <div class="ui-standings-item-stats" data-slot="item-actions">
                                        <div class="ui-card-column-header w-8 sm:w-10">Pl</div>
                                        <div class="ui-card-column-header w-8 sm:w-10">W</div>
                                        <div class="ui-card-column-header w-8 sm:w-10">D</div>
                                        <div class="ui-card-column-header hidden w-8 sm:block sm:w-10">L</div>
                                        <div class="ui-card-column-header w-8 sm:w-10">Pts</div>
                                    </div>
                                </div>
                            </div>

                            @foreach ($standingRows as $row)
                                @php
                                    $rowAccentClass = match (true) {
                                        $loop->iteration <= 2 => 'bg-emerald-500 dark:bg-emerald-400',
                                        $loop->iteration > 2 && ($standingCount - $loop->iteration) < 2 => 'bg-rose-500 dark:bg-rose-400',
                                        default => null,
                                    };
                                @endphp
                                @if ($row->can_link)
                                    <a class="ui-standings-item relative group {{ $row->withdrawn ? 'line-through' : '' }}"
                                        wire:key="section-standing-{{ $section->id }}-{{ $row->id }}"
                                        data-section-table-row-type="link"
                                        data-section-table-band
                                        data-slot="item"
                                        data-variant="muted"
                                        data-size="default"
                                        href="{{ route('team.show', $row->id) }}">
                                @else
                                    <div class="ui-standings-item relative {{ $row->withdrawn ? 'line-through' : '' }}"
                                        wire:key="section-standing-{{ $section->id }}-{{ $row->id }}"
                                        data-section-table-row-type="static"
                                        data-section-table-band
                                        data-slot="item"
                                        data-variant="muted"
                                        data-size="default">
                                @endif
                                    @if ($rowAccentClass)
                                        <span
                                            aria-hidden="true"
                                            class="absolute inset-y-2 left-0 w-1 rounded-r-full {{ $rowAccentClass }}"
                                        ></span>
                                    @endif

                                    <div class="ui-standings-item-content" data-slot="item-content">
                                        <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3">
                                            <div class="w-5 shrink-0 text-center text-sm font-semibold tabular-nums text-muted-foreground sm:w-7">
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

                                        <div class="ui-standings-item-stats" data-slot="item-actions">
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
