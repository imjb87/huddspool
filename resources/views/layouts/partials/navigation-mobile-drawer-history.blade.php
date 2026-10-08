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
    <div class="space-y-3">
        <div class="ui-card">
            <div class="ui-card-rows">
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center gap-2 text-left transition-colors"
                    @click="goBackToRoot()">
                    <span class="flex items-center gap-2" data-mobile-back-label>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-gray-400 dark:text-gray-500" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 6l-6 6l6 6" />
                        </svg>
                        History
                    </span>
                </button>
            </div>
        </div>
        <div class="ui-card">
            <div class="ui-card-rows">
            @forelse ($historySeasonGroups as $historySeasonGroup)
                <button type="button"
                    class="ui-card-row w-full cursor-pointer items-center justify-between text-left transition-colors"
                    data-mobile-history-season-trigger
                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                    <span class="min-w-0 truncate">{{ $historySeasonGroup['season']->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 6l6 6l-6 6" />
                    </svg>
                </button>
            @empty
                <div class="ui-card-row text-muted-foreground">No historical seasons yet.</div>
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
        <div class="space-y-3">
            <div class="ui-card">
                <div class="ui-card-rows">
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center gap-2 text-left transition-colors"
                        @click="openDrawer('history')">
                        <span class="flex items-center gap-2" data-mobile-back-label>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-gray-400 dark:text-gray-500" aria-hidden="true">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 6l-6 6l6 6" />
                            </svg>
                            {{ $historySeasonGroup['season']->name }}
                        </span>
                    </button>
                </div>
            </div>
            <div class="ui-card">
                <div class="ui-card-rows">
                @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                    <button type="button"
                        class="ui-card-row w-full cursor-pointer items-center justify-between text-left transition-colors"
                        data-mobile-history-ruleset-trigger
                        @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}-ruleset-{{ $historyRulesetGroup['ruleset']->id }}')">
                        <span class="min-w-0 truncate">{{ $historyRulesetGroup['ruleset']->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                    </button>
                @endforeach

                @foreach ($historySeasonGroup['knockouts'] ?? [] as $historyKnockout)
                    <a href="{{ route('history.knockout.show', ['season' => $historySeasonGroup['season'], 'knockout' => $historyKnockout]) }}"
                        class="ui-card-row-link"
                        data-mobile-history-knockout-link>
                        <div class="ui-card-row justify-start">
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
            <div class="space-y-3">
                <div class="ui-card">
                    <div class="ui-card-rows">
                        <button type="button"
                            class="ui-card-row w-full cursor-pointer items-center gap-2 text-left transition-colors"
                            @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                            <span class="flex items-center gap-2" data-mobile-back-label>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-gray-400 dark:text-gray-500" aria-hidden="true">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 6l-6 6l6 6" />
                                </svg>
                                {{ $historyRulesetGroup['ruleset']->name }}
                            </span>
                        </button>
                    </div>
                </div>
                <div class="ui-card">
                    <div class="ui-card-rows">
                    @foreach ($historyRulesetGroup['sections'] as $historySection)
                        <a href="{{ route('history.section.show', ['season' => $historySeasonGroup['season'], 'ruleset' => $historyRulesetGroup['ruleset'], 'section' => $historySection]) }}"
                            class="ui-card-row-link">
                            <div class="ui-card-row justify-start">
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
