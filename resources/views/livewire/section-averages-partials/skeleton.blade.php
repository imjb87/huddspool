<div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-section-averages-row-skeleton>
    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
        @foreach (range(1, 5) as $row)
            <div data-section-averages-row-skeleton-row>
                <div class="flex items-center gap-3 rounded-lg py-3 sm:gap-4 sm:px-3 sm:py-4" data-section-averages-band>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            <div class="h-4 w-4 shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-7"></div>
                            <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            <div class="min-w-0 space-y-2">
                                <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-32"></div>
                                <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            </div>
                        </div>
                    </div>

                    <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-5">
                        <div class="w-12 sm:w-16">
                            <div class="flex flex-col items-center gap-1">
                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                <div class="h-5 w-12 rounded-md opacity-0"></div>
                            </div>
                        </div>
                        <div class="w-12 sm:w-16">
                            <div class="flex flex-col items-center gap-1">
                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                <div class="h-5 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                            </div>
                        </div>
                        <div class="w-12 sm:w-16">
                            <div class="flex flex-col items-center gap-1">
                                <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                <div class="h-5 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
