@props([
    'compact' => false,
    'variant' => 'cloud',
])

@php
    $paddingClasses = $variant === 'section-showcase' ? 'pt-0 pb-10 sm:pb-12' : 'py-24 sm:py-32';
    $sponsors = [
        [
            'name' => 'The Pool Table Guru',
            'url' => 'https://www.thepooltableguru.co.uk/',
            'image' => asset('images/sponsors/thepooltableguru.jpg'),
            'alt' => 'The Pool Table Guru',
        ],
        [
            'name' => 'Eagle Roofing',
            'url' => 'https://www.eagle-roofing.co.uk/',
            'image' => asset('images/sponsors/eagleroofing-logo.png'),
            'alt' => 'Eagle Roofing',
        ],
        [
            'name' => 'The Bigger Boat',
            'url' => 'https://www.thebiggerboat.co.uk/',
            'image' => asset('images/sponsors/tbb-logo.svg'),
            'alt' => 'The Bigger Boat',
        ],
        [
            'name' => 'NRK Fabrication',
            'url' => 'https://www.nrkfabrication.co.uk/',
            'image' => asset('images/sponsors/nrkfabrication-logo.jpg'),
            'alt' => 'NRK Fabrication',
        ],
        [
            'name' => 'Levels Huddersfield',
            'url' => 'https://www.levelshuddersfield.co.uk/',
            'image' => asset('images/sponsors/levelshuddersfield.svg'),
            'alt' => 'Levels Huddersfield',
        ],
        [
            'name' => 'UK Plastics & Glazing Ltd',
            'url' => 'https://www.facebook.com/ukplasticsandglazingltd',
            'image' => asset('images/sponsors/ukplasticsandglazing-logo.jpeg'),
            'alt' => 'UK Plastics & Glazing Ltd',
        ],
    ];
@endphp

@if ($variant === 'section-showcase')
    <section {{ $attributes->class([$paddingClasses]) }} data-section-sponsors>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="py-8 sm:py-10">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,19rem)_minmax(0,1fr)] lg:items-start lg:gap-12">
                    <div class="max-w-sm">
                        <h2 class="text-xl font-semibold tracking-tight text-gray-900 sm:text-2xl">
                            Backing the league every week
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">
                            Local businesses supporting the league. Visit the sponsors behind the tables, fixtures and nights out.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-x-3 gap-y-4 sm:gap-x-4 sm:gap-y-5 lg:grid-cols-3" data-section-sponsors-grid>
                        @foreach ($sponsors as $sponsor)
                            <a href="{{ $sponsor['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group block"
                                data-section-sponsors-card>
                                <div class="relative flex aspect-[2/1] items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-linear-to-b from-white to-gray-50 px-4 py-4 shadow-sm ring-1 ring-black/5 transition duration-200 hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md">
                                    <img class="max-h-12 w-full object-contain transition duration-200 group-hover:scale-[1.02] sm:max-h-14"
                                        src="{{ $sponsor['image'] }}"
                                        alt="{{ $sponsor['alt'] }}">
                                </div>
                                <p class="mt-2 text-center text-sm font-medium text-gray-600 transition group-hover:text-green-900">
                                    {{ $sponsor['name'] }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    <div {{ $attributes->class(['bg-white', $paddingClasses]) }}>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <h2 class="text-center text-lg font-semibold leading-8 text-gray-900">Proudly sponsored by</h2>
            <div class="mx-auto mt-10 grid max-w-lg grid-cols-4 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-6">
                @foreach ($sponsors as $sponsor)
                    <a href="{{ $sponsor['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="col-span-2 w-full object-contain lg:col-span-1">
                        <img class="max-h-20 w-full object-contain px-3"
                            src="{{ $sponsor['image'] }}"
                            alt="{{ $sponsor['alt'] }}"
                            width="158"
                            height="48">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
