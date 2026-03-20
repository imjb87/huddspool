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
