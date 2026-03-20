<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-team-players-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Players</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current squad members and their playing record in this section.
            </p>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-zinc-800/80 lg:col-span-2">
            @foreach ($players as $player)
                @php
                    $framesPlayed = (int) $player->frames_played;
                    $framesWon = (int) $player->frames_won;
                    $framesLost = (int) $player->frames_lost;
                    $rawWonPercentage = $framesPlayed > 0 ? ($framesWon / $framesPlayed) * 100 : 0;
                    $rawLostPercentage = $framesPlayed > 0 ? ($framesLost / $framesPlayed) * 100 : 0;
                    $wonPercentage = fmod($rawWonPercentage, 1.0) === 0.0
                        ? number_format($rawWonPercentage, 0)
                        : number_format($rawWonPercentage, 1);
                    $lostPercentage = fmod($rawLostPercentage, 1.0) === 0.0
                        ? number_format($rawLostPercentage, 0)
                        : number_format($rawLostPercentage, 1);
                @endphp
                <a href="{{ route('player.show', $player) }}"
                    class="block rounded-lg py-4 transition"
                    wire:key="team-player-{{ $player->id }}">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="shrink-0">
                            <img class="h-8 w-8 rounded-full object-cover"
                                src="{{ $player->avatar_url }}"
                                alt="{{ $player->name }} avatar">
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</p>
                        </div>

                        <div class="ml-auto flex shrink-0 items-center gap-3 text-center sm:gap-4">
                            <div class="w-16">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                <div class="mt-1 flex flex-col items-center gap-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $framesPlayed }}</p>
                                    <span class="invisible inline-flex items-center rounded-md px-1 py-0.5 text-[10px] font-semibold">0%</span>
                                </div>
                            </div>
                            <div class="w-16">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                <div class="mt-1 flex flex-col items-center gap-1">
                                    <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $framesWon }}</p>
                                    <span class="inline-flex items-center rounded-md bg-green-100 px-1 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">{{ $wonPercentage }}%</span>
                                </div>
                            </div>
                            <div class="w-16">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                <div class="mt-1 flex flex-col items-center gap-1">
                                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $framesLost }}</p>
                                    <span class="inline-flex items-center rounded-md bg-red-100 px-1 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">{{ $lostPercentage }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
