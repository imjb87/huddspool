<section data-home-hero>
    @php
        $logo160PngUrl = asset('images/logo-160.png') . '?v=' . filemtime(public_path('images/logo-160.png'));
        $logo160WebpUrl = asset('images/logo-160.webp') . '?v=' . filemtime(public_path('images/logo-160.webp'));
        $logo320PngUrl = asset('images/logo-320.png') . '?v=' . filemtime(public_path('images/logo-320.png'));
        $logo320WebpUrl = asset('images/logo-320.webp') . '?v=' . filemtime(public_path('images/logo-320.webp'));

        $heroTitle = $entrySeasonCountdown
            ? 'Registration for the next season is now open'
            : 'Everything for league night, in one place.';

        $heroDescription = $entrySeasonCountdown
            ? 'League registration is now open for ' . $entrySeason->name . ' until ' . $entrySeasonCountdown['target_label'] . '. Registration covers your teams, knockout entries and the key details needed for the upcoming season.'
            : 'Tables, fixtures, results and averages for every section, with the latest league information always close at hand.';
    @endphp
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-card-branded">
            <div class="ui-section ui-card-body">
                <div class="ui-shell-grid items-center">
                    <div class="flex justify-start lg:justify-center">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-full bg-white/10 blur-3xl"></div>
                            <picture>
                                <source
                                    type="image/webp"
                                    srcset="
                                        {{ $logo160WebpUrl }} 160w,
                                        {{ $logo320WebpUrl }} 320w
                                    "
                                    sizes="(min-width: 1024px) 160px, (min-width: 640px) 144px, 128px"
                                >
                                <img class="relative w-32 drop-shadow-2xl sm:w-36 lg:w-40"
                                    src="{{ $logo320PngUrl }}"
                                    srcset="
                                        {{ $logo160PngUrl }} 160w,
                                        {{ $logo320PngUrl }} 320w
                                    "
                                    sizes="(min-width: 1024px) 160px, (min-width: 640px) 144px, 128px"
                                    width="160"
                                    height="160"
                                    loading="eager"
                                    fetchpriority="high"
                                    alt="Huddersfield Pool League logo" />
                            </picture>
                        </div>
                    </div>

                    <div class="text-left lg:col-span-2">
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
                                class="pt-1"
                            >
                                <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-green-100/70">
                                    <span>Closes in</span>
                                    <div class="flex items-center gap-3 tabular-nums" data-home-hero-entry-countdown>
                                        <span><span class="text-white" x-text="remaining.days"></span>d</span>
                                        <span><span class="text-white" x-text="remaining.hours"></span>h</span>
                                        <span><span class="text-white" x-text="remaining.minutes"></span>m</span>
                                        <span><span class="text-white" x-text="remaining.seconds"></span>s</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h1 class="text-base font-semibold text-gray-100">
                            {{ $heroTitle }}
                        </h1>
                        <p class="mt-4 text-sm leading-6">
                            {{ $heroDescription }}
                        </p>

                        @if ($entrySeasonCountdown['cta_url'] ?? null)
                            <div class="mt-4">
                                <a
                                    href="{{ $entrySeasonCountdown['cta_url'] }}"
                                    class="inline-flex items-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-green-900 shadow-sm transition duration-150 hover:-translate-y-0.5 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-white/80 focus:ring-offset-2 focus:ring-offset-green-900"
                                >
                                    {{ $entrySeasonCountdown['cta_label'] }}
                                </a>
                            </div>
                        @endif

                        @if (! ($entrySeason && $entrySeasonCountdown))
                            <p class="mt-4 text-sm leading-6 text-green-100" data-home-hero-account-link>
                                @auth
                                    Keep your details, knockouts and team tools together in your
                                    <a href="{{ route('account.show') }}" class="font-semibold text-white underline decoration-white/40 underline-offset-3 transition hover:decoration-white">
                                        account
                                    </a>.
                                @else
                                    Already involved in the league?
                                    <a href="{{ route('login') }}" class="font-semibold text-white underline decoration-white/40 underline-offset-3 transition hover:decoration-white">
                                        Log in
                                    </a>
                                    to access your account.
                                @endauth
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
