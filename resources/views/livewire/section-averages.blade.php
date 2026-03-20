<section data-section-averages-view class="mt-0">
    <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Averages</h2>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    {{ $averageSummaryCopy }}
                </p>
            </div>

            <div class="lg:col-span-2">
                <div data-section-averages-shell>
                    @include('livewire.section-averages-partials.header')

                    <div wire:loading.remove wire:target="previousPage, nextPage">
                        @if ($players->isEmpty())
                            @include('livewire.section-averages-partials.empty-state')
                        @else
                            @include('livewire.section-averages-partials.rows')
                        @endif
                    </div>

                    @include('livewire.section-averages-partials.skeleton')
                </div>

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
            </div>
        </div>
    </div>
</section>
