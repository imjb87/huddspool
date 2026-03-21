<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-{{ $sectionKey }}>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current player records for the {{ str_contains($sectionKey, 'home') ? 'home' : 'away' }} side in this section.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                @foreach ($players as $player)
                    <x-player-stats-line
                        :href="route('player.show', $player)"
                        :avatar-url="$player->avatar_url"
                        :name="$player->name"
                        :role-label="\App\Enums\UserRole::labelFor($player->role)"
                        :frames-played="$player->frames_played"
                        :frames-won="$player->frames_won"
                        :frames-lost="$player->frames_lost"
                        wire:key="{{ $sectionKey }}-{{ $player->id }}" />
                @endforeach
            </div>
        </div>
    </div>
</section>
