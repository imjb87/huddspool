<section class="border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80" data-history-season-shell>
    <button type="button"
        class="flex w-full cursor-pointer items-center justify-between gap-4 rounded-lg px-3 py-4 text-left transition hover:bg-gray-50 hover:text-gray-700 dark:hover:bg-zinc-800/70 dark:hover:text-gray-200"
        @click="openSeason = openSeason === 'season-{{ $group['season']->id }}' ? null : 'season-{{ $group['season']->id }}'; openRuleset = null"
        :aria-expanded="openSeason === 'season-{{ $group['season']->id }}'"
        data-history-season-trigger>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $group['season']->name }}</h2>
        <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform dark:text-gray-500"
            :class="{ 'rotate-90': openSeason === 'season-{{ $group['season']->id }}' }"
            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="openSeason === 'season-{{ $group['season']->id }}'" x-cloak class="pb-4"
        data-history-season-panel>
        @foreach ($group['rulesets'] as $rulesetGroup)
            @include('history.partials.ruleset-group')
        @endforeach

        @include('history.partials.knockouts')
    </div>
</section>
