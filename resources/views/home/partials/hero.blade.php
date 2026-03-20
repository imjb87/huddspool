<section data-home-hero
    class="overflow-hidden bg-linear-to-br from-green-950 via-green-800 to-green-600 pt-[72px] text-white lg:pt-[80px]">
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
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl lg:text-[2.75rem]">
                    Everything for league night, in one place.
                </h1>
                <p class="text-sm leading-7 text-green-50 sm:text-base">
                    Tables, fixtures, results and averages for every section, with the latest league information
                    always close at hand.
                </p>
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
            </div>
        </div>
    </div>
</section>
