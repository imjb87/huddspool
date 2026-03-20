@php
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

<section {{ $attributes->class(['bg-gray-50 pt-0 pb-10 sm:pb-12 dark:bg-zinc-900']) }} data-section-sponsors>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 py-8 sm:py-10 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 sm:text-base">
                    Backing the league every week
                </h2>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Local businesses supporting the league. Visit the sponsors behind the tables, fixtures and nights out.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:col-span-2 lg:grid-cols-3" data-section-sponsors-grid>
                @foreach ($sponsors as $sponsor)
                    <a href="{{ $sponsor['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group block"
                        data-section-sponsors-card>
                        <div class="flex aspect-[2/1] items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-4 transition hover:border-gray-300">
                            <img class="max-h-12 w-full object-contain sm:max-h-14"
                                src="{{ $sponsor['image'] }}"
                                alt="{{ $sponsor['alt'] }}">
                        </div>
                        <p class="mt-2 text-center text-sm font-medium text-gray-500 transition group-hover:text-gray-700">
                            {{ $sponsor['name'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
