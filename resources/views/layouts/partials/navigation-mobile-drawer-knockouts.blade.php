<div class="mobile-menu-panel absolute inset-0 overflow-y-auto px-4 py-4"
    x-show="activeDrawer === 'knockouts'"
    x-cloak
    data-mobile-knockouts-links
    data-mobile-menu-panel="knockouts"
    :class="mobileMenuPanelClasses('knockouts')"
    x-transition:enter="ui-motion-panel-in"
    x-transition:enter-start="ui-motion-panel-enter-start"
    x-transition:enter-end="ui-motion-panel-enter-end"
    x-transition:leave="ui-motion-panel-out"
    x-transition:leave-start="ui-motion-panel-leave-start"
    x-transition:leave-end="ui-motion-panel-leave-end">
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
                        Knockouts
                    </span>
                </button>
            </div>
        </div>
        <div class="ui-card">
            <div class="ui-card-rows">
            @foreach ($navigableKnockouts as $knockout)
                <a href="{{ route('knockout.show', $knockout) }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row justify-start">
                        {{ $knockout->name }}
                    </div>
                </a>
            @endforeach
            <a href="{{ route('page.show', 'knockout-dates') }}"
                class="ui-card-row-link">
                <div class="ui-card-row justify-start">
                    Knockout Dates
                </div>
            </a>
            </div>
        </div>
    </div>
</div>
