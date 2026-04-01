@if ($averages)
    <div class="block border-t border-gray-200 dark:border-neutral-800/75">
        <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-neutral-800/75">
            <div class="px-4 py-4 sm:px-5">
                <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                <p class="mt-1 text-center text-base font-semibold text-gray-900 dark:text-gray-100">{{ $averages->frames_played }}</p>
            </div>
            <div class="px-4 py-4 sm:px-5">
                <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                <div class="mt-1 flex items-center justify-center gap-2">
                    <p class="text-base font-semibold text-green-700 dark:text-green-400">{{ $averages->frames_won }}</p>
                    <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">
                        {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($averages->frames_won_percentage) }}%
                    </span>
                </div>
            </div>
            <div class="px-4 py-4 sm:px-5">
                <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                <div class="mt-1 flex items-center justify-center gap-2">
                    <p class="text-base font-semibold text-red-700 dark:text-red-400">{{ $averages->frames_lost }}</p>
                    <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">
                        {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($averages->frames_lost_percentage) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif
