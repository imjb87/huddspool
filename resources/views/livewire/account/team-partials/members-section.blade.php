<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-account-team-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Team members</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current squad members, their role on the team, and this season's P/W/L record.
            </p>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80 lg:col-span-2" data-account-team-management>
            @foreach ($this->teamMembers as $member)
                <x-player-stats-line
                    :href="route('player.show', $member)"
                    :avatar-url="$member->avatar_url"
                    :name="$member->name"
                    :role-label="\App\Enums\UserRole::labelFor($member->role)"
                    :frames-played="$member->frames_played"
                    :frames-won="$member->frames_won"
                    :frames-lost="$member->frames_lost"
                    stats-marker="account-team-member-stats"
                    wire:key="account-team-member-{{ $member->id }}" />
            @endforeach
        </div>
    </div>
</section>
