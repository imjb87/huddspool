<section class="ui-section" data-account-team-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team members</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current squad members, their role on the team, and this season's P/W/L record.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card" data-account-team-management>
                <div class="ui-card-column-headings px-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

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
                    @foreach ($this->teamMembers as $member)
                        <x-player-stats-line
                            :href="route('player.show', $member)"
                            :avatar-url="$member->avatar_url"
                            :name="$member->name"
                            :role-label="\App\Enums\UserRole::labelFor($member->role)"
                            :frames-played="$member->frames_played"
                            :frames-won="$member->frames_won"
                            :frames-lost="$member->frames_lost"
                            :show-inline-stat-labels="false"
                            wrapper-class="ui-card-row-link"
                            row-class="ui-card-row px-4 sm:px-5"
                            stats-marker="account-team-member-stats"
                            wire:key="account-team-member-{{ $member->id }}" />
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
