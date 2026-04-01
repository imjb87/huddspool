<section class="ui-section" data-venue-info-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21h7.5M12 17.25v3.75m-6-13.5h12m-12 0a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 7.5m-12 0v6A2.25 2.25 0 0 0 8.25 15.75h7.5A2.25 2.25 0 0 0 18 13.5v-6" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Venue information</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Contact details and location information for this venue.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Address</p>
                        <p class="whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ $venue->address }}</p>
                    </div>

                    <div class="mt-5">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Telephone</p>
                        @if ($venue->telephone)
                            <a href="tel:{{ $venue->telephone }}"
                                class="ui-link inline-flex text-sm font-semibold">
                                {{ $venue->telephone }}
                            </a>
                        @else
                            <p class="text-sm text-gray-900 dark:text-gray-100">Not listed</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
