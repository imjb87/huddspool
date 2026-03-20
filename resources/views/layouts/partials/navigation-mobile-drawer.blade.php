<div class="relative z-50 lg:hidden" role="dialog" aria-modal="true"
    @close.stop="closeMenu()" @keydown.escape.window="closeMenu()" x-cloak x-show="open">
    <div class="fixed inset-x-0 bottom-0 z-20 bg-gray-500/40 transition-opacity dark:bg-black/70" x-show="open"
        @click="closeMenu()"
        :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
    <div class="fixed inset-x-0 right-0 z-30 bg-white shadow-2xl ring-1 ring-black/5 dark:bg-zinc-900 dark:ring-white/10"
        @click.stop
        :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
        data-mobile-menu-drawer
        x-show="open" x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
        <div class="relative h-full overflow-hidden bg-white dark:bg-zinc-900">
            @include('layouts.partials.navigation-mobile-drawer-root')
            @include('layouts.partials.navigation-mobile-drawer-rulesets')
            @include('layouts.partials.navigation-mobile-drawer-history')
            @include('layouts.partials.navigation-mobile-drawer-knockouts')
        </div>
    </div>
</div>
