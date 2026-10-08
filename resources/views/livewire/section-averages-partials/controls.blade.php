<div class="border-t border-border px-5 py-4" data-section-averages-controls>
    <nav class="ui-pagination" aria-label="Averages pagination" data-section-averages-pagination data-section-averages-band>
        <button wire:click="previousPage" wire:loading.attr="disabled"
            class="ui-pagination-link"
            aria-label="Go to previous page"
            type="button"
            @disabled($page === 1)>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M15 6l-6 6l6 6" />
            </svg>
            <span class="hidden sm:inline">Previous</span>
        </button>

        <span class="ui-pagination-current" aria-live="polite">
            Page {{ $page }}
        </span>

        <button wire:click="nextPage" wire:loading.attr="disabled"
            class="ui-pagination-link"
            aria-label="Go to next page"
            type="button"
            @disabled($page >= $lastPage)>
            <span class="hidden sm:inline">Next</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4" aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 6l6 6l-6 6" />
            </svg>
        </button>
    </nav>
</div>
