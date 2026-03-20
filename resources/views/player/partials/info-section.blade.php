<section class="py-1" data-player-profile-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Player information</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Public profile details, current team information, and this season's playing record.
            </p>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="pt-1">
                <div class="space-y-5">
                    @include('player.partials.profile-header')
                    @include('player.partials.averages-card')
                    @include('player.partials.contact-details')
                </div>
            </div>
        </div>
    </div>
</section>
