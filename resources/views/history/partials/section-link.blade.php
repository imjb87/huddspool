<a href="{{ route('history.section.show', ['season' => $group['season'], 'ruleset' => $rulesetGroup['ruleset'], 'section' => $section]) }}"
    class="flex items-center justify-between gap-3 py-3 text-sm font-medium text-gray-700 transition hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100"
    data-history-section-link>
    <span>{{ $section->name }}</span>
    <svg class="h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
    </svg>
</a>
