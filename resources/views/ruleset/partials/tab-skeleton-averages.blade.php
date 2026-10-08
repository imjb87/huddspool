<section class="ui-section animate-pulse" data-section-tab-skeleton="averages">
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
                <div class="ui-card ui-averages-card">
                    <div class="ui-averages-item-group" data-slot="item-group">
                        <div class="ui-average-item ui-average-item-header" data-section-averages-band data-slot="item" data-variant="muted" data-size="default">
                            <div class="ui-average-item-content" data-slot="item-content">
                                <div class="flex min-w-0 items-center gap-2 sm:flex-1 sm:gap-3"></div>

                                <div class="ui-average-item-stats" data-slot="item-actions">
                                    @foreach (range(1, 3) as $column)
                                        <div class="{{ $column === 3 ? 'hidden sm:block' : '' }} w-12 sm:w-16">
                                            <div class="mx-auto h-3 w-8 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @foreach (range(1, 10) as $row)
                            <div class="ui-average-item" data-section-tab-skeleton-row="averages" data-section-averages-band data-slot="item" data-variant="muted" data-size="default">
                                <div class="ui-average-item-content" data-slot="item-content">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3">
                                            <div class="h-4 w-4 shrink-0 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="size-8 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="space-y-2">
                                                <div class="h-4 w-32 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="h-3 w-16 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ui-average-item-stats" data-slot="item-actions">
                                        <div class="w-12 sm:w-16">
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                <div class="h-5 w-12 rounded-md opacity-0"></div>
                                            </div>
                                        </div>
                                        <div class="w-12 sm:w-16">
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                <div class="h-5 w-12 rounded-md bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>
                                        </div>
                                        <div class="hidden w-12 sm:block sm:w-16">
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-10"></div>
                                                <div class="h-5 w-12 rounded-md bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-border px-5 py-4" data-section-averages-controls>
                        <div class="ui-pagination" data-section-averages-pagination data-section-averages-band>
                            <div class="h-9 min-w-24 justify-self-start rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                            <div class="h-4 w-14 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                            <div class="h-9 min-w-24 justify-self-end rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
