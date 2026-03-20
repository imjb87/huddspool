<div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-section-averages-row-skeleton>
    <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
        @foreach (range(1, 5) as $row)
            <div class="py-4" data-section-averages-row-skeleton-row>
                <div class="flex items-center gap-3 sm:gap-4" data-section-averages-band>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            <div class="h-4 w-6 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-7"></div>
                            <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    </div>

                    <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-5">
                        <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                            <div class="h-6 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                            <div class="h-6 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
