<section class="ui-section" data-player-profile-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Player information</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Public profile details, current team information, and this season's playing record.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body">
                    @include('player.partials.profile-header')
                </div>

                @if ($averages || (($player->email || $player->telephone) && auth()->check()))
                    <div class="ui-card-rows">
                        @include('player.partials.averages-card')
                        @include('player.partials.contact-details')
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
