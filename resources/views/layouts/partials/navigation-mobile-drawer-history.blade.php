<div class="absolute inset-0 overflow-y-auto px-4 py-4"
    x-show="activeDrawer === 'history'"
    x-cloak
    data-mobile-history-links
    data-mobile-menu-panel="history"
    x-transition:enter="transform transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full">
    <div class="space-y-4">
        <div class="ui-card overflow-hidden">
            <button type="button"
                class="ui-card-row w-full cursor-pointer items-center gap-3 px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                @click="goBackToRoot()">
                <span class="flex items-center gap-3" data-mobile-back-label>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                    </svg>
                    History
                </span>
            </button>
        </div>
        <div class="ui-card overflow-hidden">
            <div class="ui-card-rows">
            @forelse ($historySeasonGroups as $historySeasonGroup)
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                    data-mobile-history-season-trigger
                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                    <span>{{ $historySeasonGroup['season']->name }}</span>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            @empty
                <div class="ui-card-row px-4 text-sm text-gray-500 dark:text-gray-400 sm:px-5">No historical seasons yet.</div>
            @endforelse
            </div>
        </div>
    </div>
</div>

@foreach ($historySeasonGroups as $historySeasonGroup)
    <div class="absolute inset-0 overflow-y-auto px-4 py-4"
        x-show="activeDrawer === 'history-season-{{ $historySeasonGroup['season']->id }}'"
        x-cloak
        data-mobile-history-season-links
        data-mobile-menu-panel="history-season-{{ $historySeasonGroup['season']->id }}"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full">
        <div class="space-y-4">
            <div class="ui-card overflow-hidden">
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center gap-3 px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                    @click="openDrawer('history')">
                    <span class="flex items-center gap-3" data-mobile-back-label>
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                        </svg>
                        {{ $historySeasonGroup['season']->name }}
                    </span>
                </button>
            </div>
            <div class="ui-card overflow-hidden">
                <div class="ui-card-rows">
                @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center justify-between px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                        data-mobile-history-ruleset-trigger
                        @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}')">
                        <span>{{ $historyRulesetGroup['ruleset']->name }}</span>
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endforeach

                @foreach ($historySeasonGroup['knockouts'] ?? [] as $historyKnockout)
                    <a href="{{ route('history.knockout.show', ['season' => $historySeasonGroup['season'], 'knockout' => $historyKnockout]) }}"
                        class="ui-card-row-link"
                        data-mobile-history-knockout-link>
                        <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                            {{ $historyKnockout->name }}
                        </div>
                    </a>
                @endforeach
                </div>
            </div>
        </div>
    </div>

    @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
        <div class="absolute inset-0 overflow-y-auto px-4 py-4"
            x-show="activeDrawer === 'history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}'"
            x-cloak
            data-mobile-history-section-links
            data-mobile-menu-panel="history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full">
            <div class="space-y-4">
                <div class="ui-card overflow-hidden">
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center gap-3 px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                        @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                        <span class="flex items-center gap-3" data-mobile-back-label>
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                            </svg>
                            {{ $historyRulesetGroup['ruleset']->name }}
                        </span>
                    </button>
                </div>
                <div class="ui-card overflow-hidden">
                    <div class="ui-card-rows">
                    @foreach ($historyRulesetGroup['sections'] as $historySection)
                        <a href="{{ route('history.section.show', ['season' => $historySeasonGroup['season'], 'ruleset' => $historyRulesetGroup['ruleset'], 'section' => $historySection]) }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                                {{ $historySection->name }}
                            </div>
                        </a>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endforeach
