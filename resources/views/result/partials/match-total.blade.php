<div class="ui-card-row items-start justify-between gap-4" data-result-card-band>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match total</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $result->home_team_name }}
            <span class="text-gray-300 dark:text-neutral-600">/</span>
            {{ $result->away_team_name }}
        </p>
    </div>

    <div class="ml-auto flex shrink-0 self-center items-center text-right">
        <div class="inline-flex h-7 w-[60px] overflow-hidden rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 text-center text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10"
            data-result-score-pill>
            <div class="flex w-1/2 items-center justify-center tabular-nums pl-1">{{ $result->home_score }}</div>
            <div class="w-px bg-white/25"></div>
            <div class="flex w-1/2 items-center justify-center tabular-nums pr-1">{{ $result->away_score }}</div>
        </div>
    </div>
</div>
