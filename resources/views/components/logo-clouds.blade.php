@php
    $sponsors = [
        [
            'name' => 'The Pool Table Guru',
            'url' => 'https://www.thepooltableguru.co.uk/',
            'image' => asset('images/sponsors/thepooltableguru-320.jpg') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-320.jpg')),
            'image_96' => asset('images/sponsors/thepooltableguru-96.jpg') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-96.jpg')),
            'image_160' => asset('images/sponsors/thepooltableguru-160.jpg') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-160.jpg')),
            'image_192' => asset('images/sponsors/thepooltableguru-192.jpg') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-192.jpg')),
            'webp_96' => asset('images/sponsors/thepooltableguru-96.webp') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-96.webp')),
            'webp_160' => asset('images/sponsors/thepooltableguru-160.webp') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-160.webp')),
            'webp_192' => asset('images/sponsors/thepooltableguru-192.webp') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-192.webp')),
            'webp_320' => asset('images/sponsors/thepooltableguru-320.webp') . '?v=' . filemtime(public_path('images/sponsors/thepooltableguru-320.webp')),
            'width' => 159,
            'height' => 160,
            'alt' => '',
            'sizes' => '(min-width: 1024px) 110px, (min-width: 640px) 96px, 84px',
        ],
        [
            'name' => 'Eagle Roofing',
            'url' => 'https://www.eagle-roofing.co.uk/',
            'image' => asset('images/sponsors/eagleroofing-logo.png'),
            'width' => 290,
            'height' => 81,
            'alt' => '',
        ],
        [
            'name' => 'The Bigger Boat',
            'url' => 'https://www.thebiggerboat.co.uk/',
            'image' => asset('images/sponsors/tbb-logo.svg'),
            'width' => 287,
            'height' => 70,
            'alt' => '',
        ],
        [
            'name' => 'NRK Fabrication',
            'url' => 'https://www.nrkfabrication.co.uk/',
            'image' => asset('images/sponsors/nrkfabrication-logo-320.jpg') . '?v=' . filemtime(public_path('images/sponsors/nrkfabrication-logo-320.jpg')),
            'image_160' => asset('images/sponsors/nrkfabrication-logo-160.jpg') . '?v=' . filemtime(public_path('images/sponsors/nrkfabrication-logo-160.jpg')),
            'webp_160' => asset('images/sponsors/nrkfabrication-logo-160.webp') . '?v=' . filemtime(public_path('images/sponsors/nrkfabrication-logo-160.webp')),
            'webp_320' => asset('images/sponsors/nrkfabrication-logo-320.webp') . '?v=' . filemtime(public_path('images/sponsors/nrkfabrication-logo-320.webp')),
            'width' => 160,
            'height' => 94,
            'alt' => '',
            'sizes' => '(min-width: 1024px) 130px, (min-width: 640px) 120px, 96px',
        ],
        [
            'name' => 'Levels Huddersfield',
            'url' => 'https://www.levelshuddersfield.co.uk/',
            'image' => asset('images/sponsors/levelshuddersfield.svg'),
            'width' => 260,
            'height' => 120,
            'alt' => '',
        ],
        [
            'name' => 'UK Plastics & Glazing Ltd',
            'url' => 'https://www.facebook.com/ukplasticsandglazingltd',
            'image' => asset('images/sponsors/ukplasticsandglazing-logo-320.jpeg') . '?v=' . filemtime(public_path('images/sponsors/ukplasticsandglazing-logo-320.jpeg')),
            'image_160' => asset('images/sponsors/ukplasticsandglazing-logo-160.jpeg') . '?v=' . filemtime(public_path('images/sponsors/ukplasticsandglazing-logo-160.jpeg')),
            'webp_160' => asset('images/sponsors/ukplasticsandglazing-logo-160.webp') . '?v=' . filemtime(public_path('images/sponsors/ukplasticsandglazing-logo-160.webp')),
            'webp_320' => asset('images/sponsors/ukplasticsandglazing-logo-320.webp') . '?v=' . filemtime(public_path('images/sponsors/ukplasticsandglazing-logo-320.webp')),
            'width' => 160,
            'height' => 86,
            'alt' => '',
            'sizes' => '(min-width: 1024px) 130px, (min-width: 640px) 120px, 96px',
        ],
    ];

    $sponsorCarouselCloneCount = min(3, count($sponsors));
    $carouselSponsors = array_merge(
        array_slice($sponsors, -$sponsorCarouselCloneCount),
        $sponsors,
        array_slice($sponsors, 0, $sponsorCarouselCloneCount),
    );
@endphp

<section {{ $attributes->class(['ui-section']) }} data-section-sponsors>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rocket size-5 text-neutral-700 dark:text-neutral-200">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3" />
                        <path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3" />
                        <path d="M14 9a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                    </svg>
                </span>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">
                        Backing the league every week
                    </h2>
                    <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                        Local businesses supporting the league. Visit the sponsors behind the tables, fixtures and nights out.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card ui-sponsors-card">
                    <div class="ui-card-body">
                        <div class="ui-sponsor-carousel"
                            x-data="window.sponsorCarousel({{ count($sponsors) }}, {{ $sponsorCarouselCloneCount }})"
                            x-init="start()"
                            x-on:mouseenter="handleMouseEnter()"
                            x-on:mouseleave="handleMouseLeave()"
                            x-on:focusin="handleFocusIn()"
                            x-on:focusout="handleFocusOut($event)"
                            x-on:visibilitychange.window="handleVisibilityChange()"
                            x-on:keydown.arrow-left.prevent="previous()"
                            x-on:keydown.arrow-right.prevent="next()"
                            tabindex="0"
                            data-section-sponsors-carousel>
                            <div class="ui-sponsor-carousel-viewport"
                                role="region"
                                aria-roledescription="carousel"
                                aria-label="League sponsors">
                                <div id="sponsor-carousel-track"
                                    class="ui-sponsor-carousel-track flex -ml-3 transition-transform duration-500 ease-out"
                                    x-cloak
                                    x-ref="track"
                                    x-on:transitionend="handleTransitionEnd($event)"
                                    x-bind:class="{ 'transition-none': isJumping }"
                                    x-bind:style="`transform: translate3d(-${slideOffset()}%, 0, 0)`">
                                    @foreach ($carouselSponsors as $slideIndex => $sponsor)
                                        @php
                                            $isClone = $slideIndex < $sponsorCarouselCloneCount || $slideIndex >= $sponsorCarouselCloneCount + count($sponsors);
                                        @endphp
                                        <div class="ui-sponsor-carousel-slide min-w-0 shrink-0 grow-0 basis-1/2 pl-3 lg:basis-1/3" @if ($isClone) aria-hidden="true" @endif>
                                            <a href="{{ $sponsor['url'] }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="ui-sponsor-item group"
                                                @if (! $isClone) data-section-sponsors-card @endif
                                                @if ($isClone) tabindex="-1" @endif>
                                                <div class="ui-sponsor-card-media">
                                                    @if (isset($sponsor['webp_160']))
                                                        <picture>
                                                            <source
                                                                type="image/webp"
                                                                srcset="
                                                                    @if (isset($sponsor['webp_96'])){{ $sponsor['webp_96'] }} 96w, @endif
                                                                    {{ $sponsor['webp_160'] }} 160w,
                                                                    @if (isset($sponsor['webp_192'])){{ $sponsor['webp_192'] }} 192w, @endif
                                                                    {{ $sponsor['webp_320'] }} 320w
                                                                "
                                                                sizes="{{ $sponsor['sizes'] ?? '(min-width: 1024px) 130px, (min-width: 640px) 120px, 96px' }}"
                                                            >
                                                            <img class="ui-sponsor-logo"
                                                                src="{{ $sponsor['image'] }}"
                                                                srcset="
                                                                    @if (isset($sponsor['image_96'])){{ $sponsor['image_96'] }} 96w, @endif
                                                                    {{ $sponsor['image_160'] }} 160w,
                                                                    @if (isset($sponsor['image_192'])){{ $sponsor['image_192'] }} 192w, @endif
                                                                    {{ $sponsor['image'] }} 320w
                                                                "
                                                                sizes="{{ $sponsor['sizes'] ?? '(min-width: 1024px) 130px, (min-width: 640px) 120px, 96px' }}"
                                                                width="{{ $sponsor['width'] }}"
                                                                height="{{ $sponsor['height'] }}"
                                                                style="aspect-ratio: {{ $sponsor['width'] }} / {{ $sponsor['height'] }};"
                                                                loading="lazy"
                                                                decoding="async"
                                                                alt="{{ $sponsor['alt'] }}"
                                                                aria-hidden="true">
                                                        </picture>
                                                    @else
                                                        <img class="ui-sponsor-logo"
                                                            src="{{ $sponsor['image'] }}"
                                                            @if (isset($sponsor['width'])) width="{{ $sponsor['width'] }}" @endif
                                                            @if (isset($sponsor['height'])) height="{{ $sponsor['height'] }}" @endif
                                                            @if (isset($sponsor['width'], $sponsor['height'])) style="aspect-ratio: {{ $sponsor['width'] }} / {{ $sponsor['height'] }};" @endif
                                                            loading="lazy"
                                                            decoding="async"
                                                            alt="{{ $sponsor['alt'] }}"
                                                            aria-hidden="true">
                                                    @endif
                                                </div>
                                                <div class="ui-sponsor-content">
                                                    <p class="ui-sponsor-name">
                                                        {{ $sponsor['name'] }}
                                                    </p>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="ui-sponsor-carousel-toolbar">
                                <span class="sr-only">Browse league sponsors</span>
                                <button type="button"
                                    class="ui-sponsor-carousel-button"
                                    x-on:click="previous()"
                                    x-bind:disabled="isTransitioning"
                                    aria-label="Previous sponsors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left size-4" aria-hidden="true">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 6l-6 6l6 6" />
                                    </svg>
                                </button>
                                <button type="button"
                                    class="ui-sponsor-carousel-button"
                                    x-on:click="next()"
                                    x-bind:disabled="isTransitioning"
                                    aria-label="Next sponsors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4" aria-hidden="true">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 6l6 6l-6 6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
