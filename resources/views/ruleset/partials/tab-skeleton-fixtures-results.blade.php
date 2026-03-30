<section class="ui-section animate-pulse" data-section-tab-skeleton="fixtures-results">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                <div class="mt-1 h-4 w-52 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                <div class="mt-2 h-4 w-44 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-column-headings justify-start px-4 sm:px-5" data-section-fixtures-headings>
                        <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    </div>

                    <div class="ui-card-rows">
                        @foreach (range(1, 5) as $row)
                            <div data-section-tab-skeleton-row="fixtures-results">
                                <div class="ui-card-row items-start px-4 sm:px-5" data-section-fixtures-band>
                                    <div class="min-w-0 flex-1">
                                        <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                        <div class="mt-2 h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    </div>

                                    <div class="h-7 w-[60px] rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-5 pb-4 lg:pt-5 lg:pb-6">
                    <div class="flex items-center justify-between gap-4">
                        <div class="h-10 min-w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            <div class="h-4 w-14 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        <div class="h-10 min-w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
