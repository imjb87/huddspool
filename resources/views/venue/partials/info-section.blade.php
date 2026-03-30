<section class="ui-section" data-venue-info-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Venue information</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Contact details and location information for this venue.
            </p>
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
