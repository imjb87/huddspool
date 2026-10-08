<section class="ui-section animate-pulse" data-section-tab-skeleton="fixtures-results">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <div class="flex size-6 shrink-0 items-center justify-center">
                    <div class="size-5 rounded-sm bg-gray-200 dark:bg-neutral-800"></div>
                </div>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    <div class="h-4 w-52 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    <div class="h-4 w-44 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card ui-fixtures-card">
                    <div class="ui-fixtures-date-heading">
                        <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    </div>

                    <div class="ui-fixtures-item-group" data-section-fixtures-list>
                        @foreach (range(1, 5) as $row)
                            <div class="ui-fixture-item" data-section-tab-skeleton-row="fixtures-results" data-section-fixtures-band>
                                <div class="ui-fixture-item-content">
                                    <div class="ui-fixture-team-matchup">
                                        <div class="ui-fixture-team-names">
                                            <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="h-4 w-36 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>

                                        <div class="ui-fixture-badge-stack">
                                            <div class="h-5 min-w-7 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="h-5 min-w-7 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-border px-5 py-4" data-section-fixtures-controls>
                        <div class="ui-fixtures-pagination" data-section-fixtures-pagination>
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
