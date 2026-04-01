<section data-section-averages-view class="ui-section">
    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <div class="ui-section-intro">
                    <div class="ui-section-intro-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7.5 15.75 10.5 12l2.25 2.25 4.5-6" />
                        </svg>
                    </div>

                    <div class="ui-section-intro-copy">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Averages</h2>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ $averageSummaryCopy }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card" data-section-averages-shell>
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

                @include('livewire.section-averages-partials.controls')
            </div>
        </div>
    </div>
</section>
