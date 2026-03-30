<section data-history-season-shell>
    <button type="button"
        class="ui-card-row w-full cursor-pointer text-left transition hover:bg-gray-100 dark:hover:bg-zinc-700/85"
        @click="openSeason = openSeason === 'season-{{ $group['season']->id }}' ? null : 'season-{{ $group['season']->id }}'; openRuleset = null"
        :aria-expanded="openSeason === 'season-{{ $group['season']->id }}'"
        data-history-season-trigger>
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $group['season']->name }}</h2>
        <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform dark:text-gray-500"
            :class="{ 'rotate-90': openSeason === 'season-{{ $group['season']->id }}' }"
            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="openSeason === 'season-{{ $group['season']->id }}'" x-cloak
        data-history-season-panel>
        <div class="ui-card-rows border-t border-gray-200/80 dark:border-zinc-700/75">
            @foreach ($group['rulesets'] as $rulesetGroup)
                @include('history.partials.ruleset-group')
            @endforeach

            @include('history.partials.knockouts')
        </div>
    </div>
</section>
