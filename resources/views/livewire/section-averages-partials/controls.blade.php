<div class="pt-5 pb-4 lg:pt-5 lg:pb-6" data-section-averages-controls>
    <div class="flex items-center justify-between gap-4" data-section-averages-band>
        <button wire:click="previousPage" wire:loading.attr="disabled"
            class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
            aria-label="Previous"
            {{ $page == 1 ? 'disabled' : '' }}>
            Previous
        </button>

        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            Page {{ $page }}
        </span>

        <button wire:click="nextPage" wire:loading.attr="disabled"
            class="inline-flex w-24 cursor-pointer items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-3 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
            aria-label="Next"
            {{ $page >= $lastPage ? 'disabled' : '' }}>
            Next
        </button>
    </div>
</div>
