<section data-home-hero>
    @php
        $heroLogo160PngUrl = asset('images/logo-160.png') . '?v=' . filemtime(public_path('images/logo-160.png'));
        $heroLogo160WebpUrl = asset('images/logo-160.webp') . '?v=' . filemtime(public_path('images/logo-160.webp'));
        $heroLogo320PngUrl = asset('images/logo-320.png') . '?v=' . filemtime(public_path('images/logo-320.png'));
        $heroLogo320WebpUrl = asset('images/logo-320.webp') . '?v=' . filemtime(public_path('images/logo-320.webp'));

        $heroTitle = $entrySeasonCountdown
            ? 'Registration for the next season is now open'
            : 'Everything for league night, in one place.';

        $heroDescription = $entrySeasonCountdown
            ? 'League registration is now open for ' . $entrySeason->name . ' until ' . $entrySeasonCountdown['target_label'] . '. Registration covers your teams, knockout entries and the key details needed for the upcoming season.'
            : 'Tables, fixtures, results and averages for every section, with the latest league information always close at hand.';
    @endphp
    <div class="mx-auto flex max-w-6xl flex-col items-center gap-1.5 px-6 py-4 text-center sm:gap-2 md:py-6 lg:py-8 xl:gap-3">
        @if ($entrySeason && $entrySeasonCountdown)
            <div
                x-data="{
                    target: new Date(@js($entrySeasonCountdown['target_iso'])).getTime(),
                    remaining: { days: '00', hours: '00', minutes: '00', seconds: '00' },
                    refresh() {
                        const secondsLeft = Math.max(0, Math.floor((this.target - Date.now()) / 1000));
                        const days = Math.floor(secondsLeft / 86400);
                        const hours = Math.floor((secondsLeft % 86400) / 3600);
                        const minutes = Math.floor((secondsLeft % 3600) / 60);
                        const seconds = secondsLeft % 60;
                        this.remaining = {
                            days: String(days).padStart(2, '0'),
                            hours: String(hours).padStart(2, '0'),
                            minutes: String(minutes).padStart(2, '0'),
                            seconds: String(seconds).padStart(2, '0'),
                        };
                    },
                }"
                x-init="refresh(); setInterval(() => refresh(), 1000)"
                class="flex flex-wrap items-center justify-center gap-3 text-xs font-medium text-gray-600 dark:text-gray-400"
            >
                <span class="font-semibold text-gray-900 dark:text-gray-100">Registration open</span>
                <span>Closes in</span>
                <div class="flex items-center gap-3 tabular-nums" data-home-hero-entry-countdown>
                    <span><span class="font-semibold text-gray-900 dark:text-gray-100" x-text="remaining.days"></span>d</span>
                    <span><span class="font-semibold text-gray-900 dark:text-gray-100" x-text="remaining.hours"></span>h</span>
                    <span><span class="font-semibold text-gray-900 dark:text-gray-100" x-text="remaining.minutes"></span>m</span>
                    <span><span class="font-semibold text-gray-900 dark:text-gray-100" x-text="remaining.seconds"></span>s</span>
                </div>
            </div>
        @endif

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

        <h1 class="max-w-4xl text-3xl leading-[1.1] font-semibold tracking-tight text-balance text-gray-900 sm:text-4xl xl:text-5xl xl:tracking-tighter dark:text-gray-100">
            {{ $heroTitle }}
        </h1>
        <p class="max-w-2xl text-base leading-6 text-gray-900 sm:text-lg sm:leading-7 dark:text-gray-100">
            {{ $heroDescription }}
        </p>

        <div class="flex w-full items-center justify-center gap-2 pt-1" data-home-hero-actions>
            @if ($entrySeasonCountdown['cta_url'] ?? null)
                <a
                    href="{{ $entrySeasonCountdown['cta_url'] }}"
                    class="group/button inline-flex h-[35px] shrink-0 items-center justify-center gap-1.5 rounded-full border border-transparent bg-black px-4 text-sm leading-5 font-medium whitespace-nowrap text-white transition-all outline-none select-none hover:bg-black/80 focus-visible:ring-2 focus-visible:ring-black/50 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-200/80 dark:focus-visible:ring-gray-200/50"
                    data-home-hero-registration
                >
                    {{ $entrySeasonCountdown['cta_label'] }}
                </a>
            @else
                <a
                    href="{{ auth()->check() ? route('account.show') : route('login') }}"
                    class="group/button inline-flex h-[35px] shrink-0 items-center justify-center gap-1.5 rounded-full border border-transparent bg-black px-4 text-sm leading-5 font-medium whitespace-nowrap text-white transition-all outline-none select-none hover:bg-black/80 focus-visible:ring-2 focus-visible:ring-black/50 dark:bg-gray-200 dark:text-gray-900 dark:hover:bg-gray-200/80 dark:focus-visible:ring-gray-200/50"
                    data-home-hero-account-action
                >
                    @auth
                        View your account
                    @else
                        Log in to view your account
                    @endauth
                </a>
            @endif
        </div>
    </div>
</section>
