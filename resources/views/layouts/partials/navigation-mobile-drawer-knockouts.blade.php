<div class="{{ $mobileDrawerPanelClasses }}"
    x-show="activeDrawer === 'knockouts'"
    x-cloak
    data-mobile-knockouts-links
    data-mobile-menu-panel="knockouts"
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
                Knockouts
            </span>
        </button>
        <div class="{{ $mobileDrawerListClasses }}">
            @foreach ($navigableKnockouts as $knockout)
                <a href="{{ route('knockout.show', $knockout) }}"
                    class="{{ $mobileDrawerTextLinkClasses }}">
                    {{ $knockout->name }}
                </a>
            @endforeach
            <a href="{{ route('page.show', 'knockout-dates') }}"
                class="block rounded-lg px-0 py-3 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-900">
                Knockout Dates
            </a>
        </div>
    </div>
</div>
