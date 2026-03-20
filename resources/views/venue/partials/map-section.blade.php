<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-venue-map-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Map</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Find the venue using the embedded map.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800/80 dark:bg-zinc-800/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
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
