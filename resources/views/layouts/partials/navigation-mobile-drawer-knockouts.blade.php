<div class="absolute inset-0 overflow-y-auto px-4 py-4"
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
    <div class="space-y-4">
        <div class="ui-card overflow-hidden">
            <button type="button"
                class="ui-card-row w-full cursor-pointer items-center gap-3 px-4 text-left text-base font-semibold leading-7 text-gray-900 transition hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-neutral-800/85 sm:px-5"
                @click="goBackToRoot()">
                <span class="flex items-center gap-3" data-mobile-back-label>
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.78 4.97a.75.75 0 010 1.06L9.06 10l3.72 3.97a.75.75 0 11-1.1 1.02l-4.25-4.5a.75.75 0 010-1.04l4.25-4.5a.75.75 0 011.1-.02z" clip-rule="evenodd" />
                    </svg>
                    Knockouts
                </span>
            </button>
        </div>
        <div class="ui-card overflow-hidden">
            <div class="ui-card-rows">
            @foreach ($navigableKnockouts as $knockout)
                <a href="{{ route('knockout.show', $knockout) }}"
                    class="ui-card-row-link">
                    <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                        <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                            </svg>
                        </span>
                        <span>{{ $knockout->name }}</span>
                    </div>
                </a>
            @endforeach
            <a href="{{ route('page.show', 'knockout-dates') }}"
                class="ui-card-row-link">
                <div class="ui-card-row justify-start gap-3 px-4 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100 sm:px-5">
                    <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h9.75A2.25 2.25 0 0 1 18 4.5v15.75a.75.75 0 0 1-1.28.53l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94a.75.75 0 0 1-1.06 0l-1.94-1.94a.75.75 0 0 0-1.06 0l-1.94 1.94A.75.75 0 0 1 3.75 20.25V4.5Z" />
                        </svg>
                    </span>
                    <span>Knockout Dates</span>
                </div>
            </a>
            </div>
        </div>
    </div>
</div>
