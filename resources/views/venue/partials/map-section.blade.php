<section class="ui-section" data-venue-map-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 20.25 6-16.5m-9.568 3.794 12.536 5.452M6.75 5.25h10.5A2.25 2.25 0 0 1 19.5 7.5v9A2.25 2.25 0 0 1 17.25 18.75H6.75A2.25 2.25 0 0 1 4.5 16.5v-9A2.25 2.25 0 0 1 6.75 5.25Z" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Map</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Find the venue using the embedded map.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card overflow-hidden">
                @if (filled(config('services.google_maps.embed_key')))
                    <iframe class="h-[360px] w-full"
                        src="https://www.google.com/maps/embed/v1/place?q={{ urlencode($venue->address) }}&key={{ config('services.google_maps.embed_key') }}"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                @else
                    <div class="flex h-[360px] items-center justify-center px-6 text-center">
                        <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                            Map embedding is not configured right now.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
