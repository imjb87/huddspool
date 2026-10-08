<section class="ui-section" data-venue-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-store size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 21l18 0" />
                    <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
                    <path d="M5 21l0 -10.15" />
                    <path d="M19 21l0 -10.15" />
                    <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Venue information</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Find the venue's address, contact details, and map for match night.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                @include('venue.partials.map-section')

                <div class="ui-card-body">
                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Address</p>
                            <p class="whitespace-pre-line text-sm text-neutral-950 dark:text-neutral-50">{{ $venue->address }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Telephone</p>
                            @if ($venue->telephone)
                                <a href="tel:{{ $venue->telephone }}"
                                    class="ui-link inline-flex text-sm font-semibold">
                                    {{ $venue->telephone }}
                                </a>
                            @else
                                <p class="text-sm text-neutral-950 dark:text-neutral-50">Not listed</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
