<div class="w-full" wire:loading wire:target="searchTerm" data-search-loading-state>
    <div class="w-full space-y-3" data-search-loading-skeleton>
        @foreach (range(1, 2) as $groupIndex)
            <div class="w-full border-t border-gray-200/80 first:border-t-0 dark:border-zinc-700/75">
                <div class="ui-card-column-headings justify-start px-4 sm:px-5">
                    <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
                <div class="ui-card-rows">
                    @foreach (range(1, 3) as $rowIndex)
                        <div class="ui-card-row items-start"
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
