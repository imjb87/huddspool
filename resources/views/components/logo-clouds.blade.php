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
@endphp

<section {{ $attributes->class(['ui-section']) }} data-section-sponsors>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Backing the league every week
                </h2>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Local businesses supporting the league. Visit the sponsors behind the tables, fixtures and nights out.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card">
                    <div class="ui-card-body">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3" data-section-sponsors-grid>
                            @foreach ($sponsors as $sponsor)
                                <a href="{{ $sponsor['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex flex-col"
                                    data-section-sponsors-card>
                                    <div class="flex h-24 w-full items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 transition hover:border-gray-300 dark:border-gray-200 dark:bg-white dark:hover:border-gray-300">
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
                                                <img class="h-auto max-h-12 w-auto max-w-full object-contain sm:max-h-14"
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
                                            <img class="h-auto max-h-12 w-auto max-w-full object-contain sm:max-h-14"
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
                                    <p class="mt-2 text-center text-sm font-medium text-gray-500 transition group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200">
                                        {{ $sponsor['name'] }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
