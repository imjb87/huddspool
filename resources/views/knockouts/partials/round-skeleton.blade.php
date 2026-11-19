<section class="ui-section animate-pulse">
    <div class="ui-shell-grid">
        <div>
            <div class="ui-section-intro gap-2">
                <div class="flex size-6 shrink-0 items-center justify-center">
                    <div class="size-5 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                </div>
                <div class="grid auto-rows-min items-start gap-1.5">
                    <div class="h-5 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card ui-knockout-round-card">
                <div class="ui-knockout-match-group" data-knockout-round-body>
                    @foreach (range(1, 5) as $row)
                        <div class="ui-knockout-match-item" data-knockout-round-skeleton-row data-section-fixtures-band>
                            <div class="ui-knockout-match-content">
                                <div class="ui-knockout-match-details">
                                    <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                    <div class="ui-knockout-team-matchup mt-2">
                                        <div class="ui-knockout-team-names">
                                            <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            <div class="h-4 w-36 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        </div>

                                        <div class="ui-knockout-match-actions">
                                            <div class="ui-fixture-badge-stack">
                                                <div class="h-5 w-9 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                                <div class="h-5 w-9 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2">
                                        <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                        <div class="h-3 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-border px-3 py-4 sm:px-5">
                    <div class="ui-pagination">
                        <div class="h-9 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                        <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                        <div class="h-9 w-24 justify-self-end rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
