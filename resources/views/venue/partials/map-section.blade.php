<section class="ui-section" data-venue-map-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Map</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Find the venue using the embedded map.
            </p>
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
