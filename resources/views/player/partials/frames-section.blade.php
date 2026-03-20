@if ($frames && $frames->isNotEmpty())
    <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-player-frames-section>
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Frames</h3>
                <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Recent frames this player has played in the current section.
                </p>
            </div>

            <div class="lg:col-span-2">
                <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                    @foreach ($frames as $frame)
                        @php
                            $wonFrame = $frame->home_player_id === $player->id
                                ? $frame->home_score > $frame->away_score
                                : $frame->away_score > $frame->home_score;
                            $opponentName = $frame->home_player_id === $player->id ? $frame->away_player_name : $frame->home_player_name;
                            $opponentTeam = $frame->home_player_id === $player->id ? $frame->away_team_name : $frame->home_team_name;
                        @endphp
                        <a href="{{ route('result.show', $frame->result_id) }}"
                            class="block rounded-lg px-3 hover:bg-gray-100 dark:hover:bg-zinc-800/70"
                            wire:key="player-frame-{{ $frame->result_id }}-{{ $loop->index }}">
                            <div class="flex items-center gap-4 py-4">
                                <div class="shrink-0">
                                    <span class="inline-flex h-7 min-w-[28px] items-center justify-center rounded-full px-2 text-xs font-bold text-white shadow-sm ring-1 ring-black/10 {{ $wonFrame ? 'bg-linear-to-br from-green-900 via-green-800 to-green-700' : 'bg-linear-to-br from-red-800 via-red-700 to-red-600' }}">
                                        {{ $wonFrame ? 'W' : 'L' }}
                                    </span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $opponentName }}</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $opponentTeam }}</p>
                                </div>

                                <div class="shrink-0 text-right text-sm text-gray-500 dark:text-gray-400">
                                    {{ $frame->fixture_date->format('j M') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
