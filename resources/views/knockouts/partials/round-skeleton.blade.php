<section class="ui-section animate-pulse">
    <div class="ui-shell-grid">
        <div>
            <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            <div class="mt-2 h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-rows">
                    @foreach (range(1, 5) as $row)
                        <div class="ui-card-row items-start sm:items-center" data-knockout-round-skeleton-row data-section-fixtures-band>
                            <div class="min-w-0 flex-1">
                                <div class="space-y-2">
                                    <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-4 w-36 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>

                                <div class="mt-2 flex items-center gap-2">
                                    <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-3 w-3 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-3 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            </div>

                            <div class="h-7 w-[60px] shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
            </div>
        </div>
    </div>
</section>
