<section class="py-1" data-venue-info-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Venue information</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Contact details and location information for this venue.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ $venue->address }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Telephone</p>
                    @if ($venue->telephone)
                        <a href="tel:{{ $venue->telephone }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $venue->telephone }}
                        </a>
                    @else
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Not listed</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
