<div class="animate-pulse" wire:loading.block wire:target="previousPage, nextPage" data-section-averages-row-skeleton>
    <div class="ui-averages-item-group" data-slot="item-group">
        @include('livewire.section-averages-partials.header')

        @foreach (range(1, 10) as $row)
            <div class="ui-average-item" data-section-averages-row-skeleton-row data-section-averages-band data-slot="item" data-variant="muted" data-size="default">
                <div class="ui-average-item-content" data-slot="item-content">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            <div class="h-4 w-4 shrink-0 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-7"></div>
                            <div class="size-8 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
                            <div class="min-w-0 space-y-2">
                                <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-neutral-800 sm:w-32"></div>
                                <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-neutral-800"></div>
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
</div>
