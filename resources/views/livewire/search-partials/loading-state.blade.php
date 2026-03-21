<div class="w-full px-3 py-3 sm:px-4" wire:loading wire:target="searchTerm" data-search-loading-state>
    <div class="w-full space-y-3" data-search-loading-skeleton>
        @foreach (range(1, 2) as $groupIndex)
            <div class="w-full border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80">
                <div class="px-1 pb-2 pt-3">
                    <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
                <div class="w-full space-y-1">
                    @foreach (range(1, 3) as $rowIndex)
                        <div class="flex w-full items-start justify-between gap-4 rounded-lg border border-transparent px-4 py-3"
                            wire:key="search-loading-group-{{ $groupIndex }}-row-{{ $rowIndex }}">
                            <div class="min-w-0 flex flex-1 items-start gap-3">
                                <div class="h-9 w-9 shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                <div class="min-w-0 flex-1 space-y-2">
                                    <div class="h-3.5 w-32 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-40"></div>
                                    <div class="h-3 w-24 rounded-full bg-gray-100 dark:bg-zinc-800/70 sm:w-28"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
