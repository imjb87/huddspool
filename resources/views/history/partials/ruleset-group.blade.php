<div class="ml-4 border-t border-gray-200 first:border-t-0 dark:border-zinc-800/80 sm:ml-6"
    data-history-ruleset-shell>
    <button type="button"
        class="flex w-full cursor-pointer items-center justify-between gap-4 rounded-lg px-3 py-3 text-left transition hover:bg-gray-200/70 hover:text-gray-700 dark:hover:bg-zinc-800/70 dark:hover:text-gray-200"
        @click="openRuleset = openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' ? null : 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
        :aria-expanded="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
        data-history-ruleset-trigger>
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $rulesetGroup['ruleset']->name }}</h3>
        <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform dark:text-gray-500"
            :class="{ 'rotate-90': openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}' }"
            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="openRuleset === 'ruleset-{{ $group['season']->id }}-{{ $rulesetGroup['ruleset']->id }}'"
        x-cloak
        class="pb-1"
        data-history-ruleset-panel>
        <div class="ml-4 divide-y divide-gray-200 dark:divide-zinc-800/80 sm:ml-6">
            @foreach ($rulesetGroup['sections'] as $section)
                @include('history.partials.section-link')
            @endforeach
        </div>
    </div>
</div>
