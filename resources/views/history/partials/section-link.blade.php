<a href="{{ route('history.section.show', ['season' => $group['season'], 'ruleset' => $rulesetGroup['ruleset'], 'section' => $section]) }}"
    class="ui-card-row-link"
    data-history-section-link>
    <div class="ui-card-row">
        <span class="pl-8 text-sm font-medium text-gray-700 dark:text-gray-300 sm:pl-10">{{ $section->name }}</span>
        <svg class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
        </svg>
    </div>
</a>
