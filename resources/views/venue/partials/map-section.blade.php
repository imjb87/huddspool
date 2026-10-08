<div class="aspect-video w-full border-b border-border bg-muted" data-venue-map-section>
    @if (filled(config('services.google_maps.embed_key')))
        <iframe class="h-full w-full border-0"
            src="https://www.google.com/maps/embed/v1/place?q={{ urlencode($venue->address) }}&key={{ config('services.google_maps.embed_key') }}"
            title="{{ $venue->name }} map"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    @else
        <div class="flex h-full items-center justify-center px-6 text-center">
            <p class="max-w-sm text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                The venue map is unavailable, but the address is listed above.
            </p>
        </div>
    @endif
</div>
