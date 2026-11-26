<section class="home-hero relative isolate overflow-hidden" data-home-hero>
    @include('home.partials.orbits')
    @php
        $heroLogo160PngUrl = asset('images/logo-160.png') . '?v=' . filemtime(public_path('images/logo-160.png'));
        $heroLogo160WebpUrl = asset('images/logo-160.webp') . '?v=' . filemtime(public_path('images/logo-160.webp'));
        $heroLogo320PngUrl = asset('images/logo-320.png') . '?v=' . filemtime(public_path('images/logo-320.png'));
        $heroLogo320WebpUrl = asset('images/logo-320.webp') . '?v=' . filemtime(public_path('images/logo-320.webp'));

        $heroTitle = 'Home to Europe’s biggest pool league';
        $heroDescription = 'More than 1,000 people use Huddspool every week to follow fixtures, results, tables and player averages, all in one place';
    @endphp
    <div class="home-hero-content relative z-10 mx-auto flex w-full max-w-6xl flex-col items-center justify-center gap-4 px-6 py-16 text-center sm:gap-5 sm:py-20 lg:py-24">
        <div class="flex justify-center" data-home-hero-logo>
            <picture>
                <source
                    type="image/webp"
                    srcset="
                        {{ $heroLogo160WebpUrl }} 160w,
                        {{ $heroLogo320WebpUrl }} 320w
                    "
                    sizes="(min-width: 1024px) 144px, (min-width: 640px) 128px, 112px"
                >
                <img
                    class="h-24 w-24 object-contain sm:h-28 sm:w-28 lg:h-32 lg:w-32"
                    src="{{ $heroLogo320PngUrl }}"
                    srcset="
                        {{ $heroLogo160PngUrl }} 160w,
                        {{ $heroLogo320PngUrl }} 320w
                    "
                    sizes="(min-width: 1024px) 144px, (min-width: 640px) 128px, 112px"
                    width="160"
                    height="160"
                    loading="eager"
                    fetchpriority="high"
                    alt="Huddersfield Pool League logo"
                >
            </picture>
        </div>

        <h1 class="max-w-2xl text-3xl leading-[1.1] font-semibold tracking-tight text-balance text-gray-900 sm:text-4xl xl:text-5xl xl:tracking-tighter dark:text-gray-100" data-home-hero-title>
            {{ $heroTitle }}
        </h1>
        <p class="max-w-lg text-base leading-6 text-gray-600 sm:text-lg sm:leading-7 dark:text-gray-400" data-home-hero-description>
            {{ $heroDescription }}
        </p>

        <div class="flex w-full items-center justify-center gap-2 pt-1" data-home-hero-actions>
            <a
                href="{{ auth()->check() ? route('account.show') : route('login') }}"
                class="group/button inline-flex h-[35px] shrink-0 items-center justify-center gap-1.5 rounded-full border border-transparent bg-black px-4 text-sm leading-5 font-medium whitespace-nowrap text-white transition-[background-color,box-shadow,transform] duration-150 outline-none select-none hover:bg-black/80 focus-visible:ring-2 focus-visible:ring-black/50 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-200/80 dark:focus-visible:ring-gray-200/50"
                data-home-hero-account-action
            >
                @auth
                    View your account
                @else
                    Log in to view your account
                @endauth
            </a>
        </div>
    </div>
</section>
