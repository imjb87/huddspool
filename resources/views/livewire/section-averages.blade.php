<section data-section-averages-view class="ui-section">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="ui-section-intro gap-2">
                    <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-graph size-5 text-neutral-700 dark:text-neutral-200">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 18v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2" />
                            <path d="M7 14l3 -3l2 2l3 -3l2 2" />
                        </svg>
                    </span>

                    <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                        <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Averages</h2>
                        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                            {{ $averageSummaryCopy }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card ui-averages-card" data-section-averages-shell>
                    <div wire:loading.remove wire:target="previousPage, nextPage">
                        @if ($players->isEmpty())
                            @include('livewire.section-averages-partials.empty-state')
                        @else
                            @include('livewire.section-averages-partials.rows')
                        @endif
                    </div>

                    @include('livewire.section-averages-partials.skeleton')

                    @include('livewire.section-averages-partials.controls')
                </div>
            </div>
        </div>
    </div>
</section>
