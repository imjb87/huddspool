<div class="{{ $mobileDrawerPanelClasses }}"
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
    <div class="{{ $mobileDrawerPanelContentClasses }}">
        <button type="button"
            class="{{ $mobileDrawerBackButtonClasses }}"
            @click="goBackToRoot()">
            <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                </svg>
                History
            </span>
        </button>
        <div class="{{ $mobileDrawerListClasses }}">
            @forelse ($historySeasonGroups as $historySeasonGroup)
                <button type="button"
                    class="{{ $mobileDrawerLinkClasses }}"
                    data-mobile-history-season-trigger
                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                    <span>{{ $historySeasonGroup['season']->name }}</span>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            @empty
                <span class="block py-3 text-sm text-gray-500 dark:text-gray-400">No historical seasons yet.</span>
            @endforelse
        </div>
    </div>
</div>

@foreach ($historySeasonGroups as $historySeasonGroup)
    <div class="{{ $mobileDrawerPanelClasses }}"
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
        <div class="{{ $mobileDrawerPanelContentClasses }}">
            <button type="button"
                class="{{ $mobileDrawerBackButtonClasses }}"
                @click="openDrawer('history')">
                <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                    </svg>
                    {{ $historySeasonGroup['season']->name }}
                </span>
            </button>
            <div class="{{ $mobileDrawerListClasses }}">
                @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
                    <button type="button"
                        class="{{ $mobileDrawerLinkClasses }}"
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
                        class="{{ $mobileDrawerTextLinkClasses }}"
                        data-mobile-history-knockout-link>
                        {{ $historyKnockout->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @foreach ($historySeasonGroup['rulesets'] as $historyRulesetGroup)
        <div class="{{ $mobileDrawerPanelClasses }}"
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
            <div class="{{ $mobileDrawerPanelContentClasses }}">
                <button type="button"
                    class="{{ $mobileDrawerBackButtonClasses }}"
                    @click="openDrawer('history-season-{{ $historySeasonGroup['season']->id }}')">
                    <span class="{{ $mobileDrawerBackLabelClasses }}" data-mobile-back-label>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                        </svg>
                        {{ $historyRulesetGroup['ruleset']->name }}
                    </span>
                </button>
                <div class="{{ $mobileDrawerListClasses }}">
                    @foreach ($historyRulesetGroup['sections'] as $historySection)
                        <a href="{{ route('history.section.show', ['season' => $historySeasonGroup['season'], 'ruleset' => $historyRulesetGroup['ruleset'], 'section' => $historySection]) }}"
                            class="{{ $mobileDrawerTextLinkClasses }}">
                            {{ $historySection->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@endforeach
