<div data-history-ruleset-shell>
    <button type="button"
        class="ui-card-row w-full cursor-pointer text-left transition hover:bg-gray-100 dark:hover:bg-neutral-800/85"
        @click="openRuleset = openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' ? null : 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
        :aria-expanded="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
        data-history-ruleset-trigger>
        <h3 class="pl-4 text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">{{ $rulesetGroup['ruleset']->name }}</h3>
        <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform dark:text-gray-500"
            :class="{ 'rotate-90': openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' }"
            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
        x-cloak
        data-history-ruleset-panel>
        <div class="ui-card-rows border-t border-gray-200/80 dark:border-neutral-800/75">
            @foreach ($rulesetGroup['sections'] as $section)
                @include('history.partials.section-link')
            @endforeach
        </div>
    </div>
</div>
