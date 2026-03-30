<div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-section-averages-controls>
    <div class="flex items-center justify-between gap-4" data-section-averages-band>
        <button wire:click="previousPage" wire:loading.attr="disabled"
            class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
            aria-label="Previous"
            {{ $page == 1 ? 'disabled' : '' }}>
            Previous
        </button>

        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Page {{ $page }}
        </span>

        <button wire:click="nextPage" wire:loading.attr="disabled"
            class="ui-button-primary min-w-24 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
            aria-label="Next"
            {{ $page >= $lastPage ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
