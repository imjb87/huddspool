<div class="relative z-50 lg:hidden" role="dialog" aria-modal="true"
    @close.stop="closeMenu()" @keydown.escape.window="closeMenu()" x-cloak x-show="open">
    <div class="fixed inset-x-0 bottom-0 z-20 bg-black/20 transition-opacity dark:bg-black/60" x-show="open"
        @click="closeMenu()"
        :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
        x-transition:enter="ui-motion-fade-in" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ui-motion-fade-out"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
    <div class="fixed inset-x-0 right-0 z-30 overflow-hidden bg-white shadow-xl dark:bg-neutral-950"
        @click.stop
        :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
        data-mobile-menu-drawer
        x-show="open" x-transition:enter="ui-motion-mobile-menu-in"
        x-transition:leave="ui-motion-mobile-menu-out">
        <div class="navigation-mobile-menu relative h-full overflow-hidden bg-white dark:bg-neutral-950">
            @include('layouts.partials.navigation-mobile-drawer-root')
            @include('layouts.partials.navigation-mobile-drawer-official')
            @include('layouts.partials.navigation-mobile-drawer-rulesets')
            @include('layouts.partials.navigation-mobile-drawer-history')
            @include('layouts.partials.navigation-mobile-drawer-knockouts')
        </div>
    </div>
</div>
