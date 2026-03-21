<div class="hidden lg:ml-12 lg:flex lg:items-center lg:gap-x-6">
    @foreach ($navigationRulesets as $navigationRuleset)
        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
            <button type="button"
                class="flex items-center gap-x-1 text-sm font-semibold leading-6 transition {{ $navigationRuleset['is_active'] ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}"
                @click="open = ! open" :aria-expanded="open">
                {{ $navigationRuleset['ruleset']->name }}
                <svg class="h-4 w-4 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="absolute left-0 top-full z-10 mt-3 w-72 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5 dark:bg-zinc-900 dark:ring-white/10"
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1">
                @foreach ($navigationRuleset['sections'] as $section)
                    <a href="{{ route('ruleset.section.show', ['ruleset' => $navigationRuleset['ruleset'], 'section' => $section]) }}"
                        class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                        {{ $section->name }}
                    </a>
                @endforeach
                <div class="mx-2 my-1 border-t border-gray-200 dark:border-gray-800"></div>
                <a href="{{ route('ruleset.show', $navigationRuleset['ruleset']) }}"
                    class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                    {{ $navigationRuleset['ruleset']->name }}
                </a>
            </div>
        </div>
    @endforeach

    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
        <button type="button"
            class="flex items-center gap-x-1 text-sm font-semibold leading-6 transition {{ $knockoutNavIsActive ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}"
            @click="open = ! open" :aria-expanded="open">
            Knockouts
            <svg class="h-4 w-4 flex-none text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <div class="absolute left-0 top-full z-10 mt-3 w-72 rounded-2xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5 dark:bg-zinc-900 dark:ring-white/10"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            data-knockouts-nav>
            @foreach ($navigableKnockouts as $knockout)
                <a href="{{ route('knockout.show', $knockout) }}"
                    class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                    {{ $knockout->name }}
                </a>
            @endforeach
            @if ($navigableKnockouts->isNotEmpty())
                <div class="mx-2 my-1 border-t border-gray-200 dark:border-gray-800"></div>
            @endif
            <a href="{{ route('page.show', 'knockout-dates') }}"
                class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-50 dark:text-gray-100 dark:hover:bg-zinc-800">
                Knockout Dates
            </a>
        </div>
    </div>
    <a href="{{ route('history.index') }}"
        class="text-sm font-semibold leading-6 transition {{ $historyNavIsActive ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}">
        History
    </a>
    <a href="{{ route('page.show', 'handbook') }}"
        class="text-sm font-semibold leading-6 transition {{ $handbookNavIsActive ? 'text-green-700 dark:text-green-500' : 'text-gray-900 hover:text-green-700 dark:text-gray-100 dark:hover:text-green-500' }}">
        Handbook
    </a>
</div>
