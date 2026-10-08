<div class="min-h-80 w-full" wire:loading wire:target="searchTerm" data-search-loading-state>
    <div class="w-full space-y-1" data-search-loading-skeleton aria-hidden="true">
        @foreach (['players', 'teams'] as $groupName)
            <div class="w-full">
                <div class="px-3 pt-3 pb-1">
                    <div class="h-3 w-20 animate-pulse rounded-md bg-gray-200/80 dark:bg-neutral-800/80"></div>
                </div>
                <div class="space-y-0.5">
                    @foreach (range(1, 3) as $rowIndex)
                        <div class="flex h-9 items-center justify-between gap-4 rounded-md border border-transparent px-3"
                            wire:key="search-loading-group-{{ $groupName }}-row-{{ $rowIndex }}">
                            <div class="flex min-w-0 flex-1 items-center gap-2">
                                @if ($groupName === 'players')
                                    <div class="size-6 shrink-0 animate-pulse rounded-full bg-gray-200/80 dark:bg-neutral-800/80"></div>
                                @endif
                                <div class="h-3.5 w-32 animate-pulse rounded-md bg-gray-200/80 dark:bg-neutral-800/80 sm:w-40"></div>
                            </div>
                            <div class="h-3 w-24 animate-pulse rounded-md bg-gray-200/80 dark:bg-neutral-800/80 sm:w-28"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
