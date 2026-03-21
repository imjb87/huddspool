<section data-home-hero
    class="overflow-hidden bg-linear-to-br from-green-950 via-green-800 to-green-600 pt-[72px] text-white lg:pt-[80px]">
    @php
        $heroTitle = $entrySeasonCountdown
            ? 'Registration for the next season is now open'
            : 'Everything for league night, in one place.';

        $heroDescription = $entrySeasonCountdown
            ? 'League registration is now open for ' . $entrySeason->name . ' until ' . $entrySeasonCountdown['target_label'] . '. Registration covers your teams, knockout entries and the key details needed for the upcoming season.'
            : 'Tables, fixtures, results and averages for every section, with the latest league information always close at hand.';
    @endphp
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-12 lg:px-6 lg:py-14">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="flex justify-start">
                <div class="relative">
                    <div class="absolute inset-0 rounded-full bg-white/10 blur-3xl"></div>
                    <img class="relative w-36 drop-shadow-2xl sm:w-40 lg:w-44"
                        src="{{ asset('images/logo.png') }}" alt="Huddersfield Pool League logo" />
                </div>
            </div>

            <div class="space-y-3 text-left lg:col-span-2 lg:pr-12 xl:pr-16">
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
                        class="space-y-2 pt-1"
                    >
                        <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium uppercase tracking-[0.18em] text-green-100/80">
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

                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl lg:text-[2.75rem]">
                    {{ $heroTitle }}
                </h1>
                <p class="text-sm leading-7 text-green-50 sm:text-base">
                    {{ $heroDescription }}
                </p>

                @if ($entrySeasonCountdown['cta_url'] ?? null)
                    <div class="pt-1">
                        <a
                            href="{{ $entrySeasonCountdown['cta_url'] }}"
                            class="inline-flex items-center rounded-lg bg-white px-3.5 py-2 text-sm font-semibold text-green-900 transition hover:bg-green-50"
                        >
                            {{ $entrySeasonCountdown['cta_label'] }}
                        </a>
                    </div>
                @endif

                @if (! ($entrySeason && $entrySeasonCountdown))
                    <p class="text-sm leading-6 text-green-100" data-home-hero-account-link>
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
</section>
