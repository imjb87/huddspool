@foreach ($navigationRulesets as $navigationRuleset)
    <div class="absolute inset-0 overflow-y-auto px-4 py-4"
        x-show="activeDrawer === 'ruleset-{{ $navigationRuleset['id'] }}'"
        x-cloak
        data-mobile-ruleset-sections
        data-mobile-menu-panel="ruleset-{{ $navigationRuleset['id'] }}"
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
                        {{ $navigationRuleset['name'] }}
                    </span>
                </button>
            </div>
            <div class="ui-card overflow-hidden">
                <div class="ui-card-rows">
                @foreach ($navigationRuleset['sections'] as $section)
                    <a href="{{ route('ruleset.section.show', ['ruleset' => $navigationRuleset['ruleset'], 'section' => $section]) }}"
                        class="ui-card-row-link">
                        <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                            {{ $section->name }}
                        </div>
                    </a>
                @endforeach
                <a href="{{ route('ruleset.rules', $navigationRuleset['ruleset']) }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        {{ $navigationRuleset['name'] }}
                    </div>
                </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
