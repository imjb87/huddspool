<section class="ui-section" data-team-players-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Players</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current squad members and their playing record in this section.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-column-headings px-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3 sm:gap-4"></div>

                    <div class="ml-auto flex shrink-0 items-start gap-2 text-center sm:gap-5">
                        <div class="w-12 sm:w-16">
                            <p class="ui-card-column-header">Played</p>
                        </div>
                        <div class="w-12 sm:w-16">
                            <p class="ui-card-column-header">Won</p>
                        </div>
                        <div class="w-12 sm:w-16">
                            <p class="ui-card-column-header">Lost</p>
                        </div>
                    </div>
                </div>

                <div class="ui-card-rows">
                    @foreach ($players as $player)
                        <x-player-stats-line
                            :href="route('player.show', $player)"
                            :avatar-url="$player->avatar_url"
                            :name="$player->name"
                            :role-label="\App\Enums\UserRole::labelFor($player->role)"
                            :frames-played="$player->frames_played"
                            :frames-won="$player->frames_won"
                            :frames-lost="$player->frames_lost"
                            :show-inline-stat-labels="false"
                            wrapper-class="ui-card-row-link"
                            row-class="ui-card-row items-center px-4 sm:px-5"
                            wire:key="team-player-{{ $player->id }}" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
