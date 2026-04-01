<section class="ui-section" data-account-team-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.742-.478 3 3 0 0 0-4.682-2.72m.94 3.198v-.75A2.25 2.25 0 0 0 15.75 15.72h-7.5A2.25 2.25 0 0 0 6 17.97v.75m12 0a9.094 9.094 0 0 1-12 0m12 0a9.094 9.094 0 0 0-12 0m8.25-10.47a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team members</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Current squad members, their role on the team, and this season's P/W/L record.
                </p>
            </div>
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
