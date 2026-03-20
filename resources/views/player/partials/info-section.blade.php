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
                    <div class="flex items-start gap-8">
                        <div class="shrink-0">
                            <img class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700/80"
                                src="{{ $player->avatar_url }}"
                                alt="{{ $player->name }} avatar">
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="grid grid-cols-2 gap-x-6 gap-y-5">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</p>
                                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $player->name }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $player->roleLabel() }}</p>
                                </div>

                                <div class="col-span-2 min-w-0">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Team</p>
                                    @if ($player->team)
                                        <a href="{{ route('team.show', $player->team) }}"
                                            class="mt-2 inline-flex max-w-full text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                            <span>{{ $player->team->name }}</span>
                                        </a>
                                    @else
                                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Free agent</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($averages)
                        <div class="pt-2 pb-2 sm:col-span-2">
                            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-800/80 dark:bg-zinc-800/75 dark:ring-1 dark:ring-white/5">
                                <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-zinc-800/80">
                                    <div class="px-4 py-4 sm:px-5">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                        <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $averages->frames_played }}</p>
                                    </div>
                                    <div class="px-4 py-4 sm:px-5">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                        <div class="mt-1 flex items-end justify-between gap-2">
                                            <p class="text-base font-semibold text-green-700 dark:text-green-400">{{ $averages->frames_won }}</p>
                                            <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">
                                                {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($averages->frames_won_percentage) }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="px-4 py-4 sm:px-5">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                        <div class="mt-1 flex items-end justify-between gap-2">
                                            <p class="text-base font-semibold text-red-700 dark:text-red-400">{{ $averages->frames_lost }}</p>
                                            <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">
                                                {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($averages->frames_lost_percentage) }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($player->email && auth()->check())
                        <div class="sm:col-span-2">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email address</p>
                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                                <a href="mailto:{{ $player->email }}"
                                    class="underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                    {{ $player->email }}
                                </a>
                            </p>
                        </div>
                    @endif

                    @if ($player->telephone && auth()->check())
                        <div class="sm:col-span-2">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone number</p>
                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                                <a href="tel:{{ $player->telephone }}"
                                    class="underline decoration-gray-300 underline-offset-3 transition hover:text-gray-700 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                    {{ $player->telephone }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
