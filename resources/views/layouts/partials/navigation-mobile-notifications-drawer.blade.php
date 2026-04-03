@auth
    <div class="relative z-50 lg:hidden" role="dialog" aria-modal="true"
        @close.stop="closeNotificationsDrawer()" @keydown.escape.window="closeNotificationsDrawer()" x-cloak x-show="notificationsOpen">
        <div class="fixed inset-x-0 bottom-0 z-20 bg-gray-500/40 transition-opacity dark:bg-black/70" x-show="notificationsOpen"
            @click="closeNotificationsDrawer()"
            :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-x-0 right-0 z-30 bg-neutral-50 shadow-2xl ring-1 ring-black/5 dark:bg-neutral-950"
            @click.stop
            :style="`top: ${headerHeight}px; height: calc(100dvh - ${headerHeight}px);`"
            data-mobile-notifications-drawer
            data-mobile-notifications-links
            data-mobile-menu-panel="notifications"
            x-show="notificationsOpen" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="h-full overflow-y-auto bg-neutral-50 px-4 py-4 dark:bg-neutral-950">
                <div class="space-y-4">
                    <div class="ui-card overflow-hidden">
                        <div class="ui-card-row justify-between px-4 ring-1 ring-inset ring-gray-200 sm:px-5 dark:ring-neutral-800">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex size-8 items-center justify-center rounded-full ring-1 ring-gray-200 dark:ring-neutral-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-neutral-600 dark:text-neutral-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 0 0-12 0v.05c0 .237 0 .476-.003.713A8.967 8.967 0 0 1 3.69 15.772a23.852 23.852 0 0 0 5.454 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </span>
                                <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Notifications</p>
                            </div>
                            <template x-if="$store.headerNotifications.unreadCount > 0">
                                <button type="button"
                                    class="text-xs font-medium text-neutral-500 underline underline-offset-2 transition hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                                    @click="$store.headerNotifications.markAllAsRead()">
                                    Mark all as read
                                </button>
                            </template>
                        </div>

                        <div class="ui-card-rows">
                            <template x-if="$store.headerNotifications.notifications.length === 0">
                                <div class="ui-card-row px-4 py-5 text-sm text-neutral-500 dark:text-neutral-400 sm:px-5">
                                    You're all caught up. Match reminders, result updates, and knockout activity will show up here when something needs your attention.
                                </div>
                            </template>

                            <template x-for="notification in $store.headerNotifications.notifications" :key="notification.id">
                                <a :href="notification.open_url"
                                    class="ui-card-row-link">
                                    <div class="ui-card-row items-start px-4 sm:px-5">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <p class="min-w-0 text-sm font-semibold text-neutral-900 dark:text-neutral-100" x-text="notification.title || 'Notification'"></p>
                                                <template x-if="! notification.read">
                                                    <span class="mt-1 inline-flex size-2.5 shrink-0 rounded-full bg-green-600" aria-hidden="true"></span>
                                                </template>
                                            </div>
                                            <template x-if="notification.body">
                                                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300" x-text="notification.body"></p>
                                            </template>
                                            <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400" x-text="notification.created_at_human"></p>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endauth
