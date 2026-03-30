<section class="ui-section" data-player-profile-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Player information</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Public profile details, current team information, and this season's playing record.
            </p>
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
