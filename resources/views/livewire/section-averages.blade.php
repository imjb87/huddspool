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

                @include('livewire.section-averages-partials.controls')
            </div>
        </div>
    </div>
</section>
