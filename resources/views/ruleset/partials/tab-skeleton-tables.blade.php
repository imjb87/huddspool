<section class="ui-section animate-pulse" data-section-tab-skeleton="tables">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <div class="flex size-6 shrink-0 items-center justify-center">
                    <div class="size-5 rounded-sm bg-gray-200 dark:bg-neutral-800"></div>
                </div>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <div class="h-4 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    <div class="h-4 w-48 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card ui-standings-card">
                    <div class="ui-standings-item-group" data-slot="item-group">
                        <div class="ui-standings-item ui-standings-item-header" data-section-table-band data-slot="item" data-variant="muted" data-size="default">
                            <div class="ui-standings-item-content" data-slot="item-content">
                                <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3"></div>

                                <div class="ui-standings-item-stats" data-slot="item-actions">
                                    @foreach (range(1, 5) as $column)
                                        <div class="{{ $column === 4 ? 'hidden sm:block' : '' }} w-8 sm:w-10">
                                            <div class="mx-auto h-3 w-6 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @foreach (range(1, 10) as $row)
                            <div class="ui-standings-item relative" data-section-tab-skeleton-row="tables" data-section-table-band data-slot="item" data-variant="muted" data-size="default">
                                <div class="ui-standings-item-content" data-slot="item-content">
                                    <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                                        <div class="h-4 w-4 shrink-0 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-36"></div>
                                    </div>

                                    <div class="ui-standings-item-stats" data-slot="item-actions">
                                        @foreach (range(1, 5) as $column)
                                            <div class="{{ $column === 4 ? 'hidden sm:block' : '' }} w-8 sm:w-10">
                                                <div class="mx-auto h-4 w-4 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-5"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
