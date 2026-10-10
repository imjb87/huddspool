@auth
    <div class="relative shrink-0"
        x-data="notificationsDrawer()"
        x-init="configure({
            summaryUrl: @js(route('account.notifications.summary')),
            readAllUrl: @js(route('account.notifications.read-all')),
            readUrlTemplate: @js(route('account.notifications.read', ['notification' => '__NOTIFICATION__'])),
        })"
        @keydown.escape.window="close()">
        <button type="button"
            class="relative inline-flex size-8 shrink-0 items-center justify-center rounded-lg text-gray-900 outline-none transition-colors hover:bg-transparent hover:text-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/20 dark:text-gray-100 dark:hover:bg-transparent dark:hover:text-gray-100 dark:focus-visible:ring-gray-100/20"
            @click="toggle()"
            :aria-expanded="open"
            aria-controls="notifications-drawer"
            aria-label="Open notifications"
            :aria-label="open ? 'Close notifications' : 'Open notifications'"
            data-header-notifications-trigger>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true" data-header-action-icon="notifications" data-header-action-icon-state="closed">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <g data-header-action-icon-group>
                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" data-header-action-icon-primary />
                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" data-header-action-icon-secondary />
                </g>
            </svg>
            <span x-show="$store.headerNotifications.unreadCount > 0"
                x-cloak
                class="absolute right-0.5 top-0.5 inline-flex min-w-3.5 items-center justify-center rounded-full bg-red-600 px-1 text-[9px] leading-3 font-semibold text-white ring-2 ring-white dark:ring-neutral-950"
                x-text="$store.headerNotifications.unreadCount > 99 ? '99+' : $store.headerNotifications.unreadCount"
                data-unread-notifications-badge
                aria-hidden="true"></span>
            <span class="sr-only" x-text="`${$store.headerNotifications.unreadCount} unread notifications`"></span>
        </button>

        <div x-show="open"
            x-cloak
            x-transition:enter="ui-motion-fade-in"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ui-motion-fade-out"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-x-0 bottom-0 z-[60] bg-black/20 dark:bg-black/60"
            style="top: var(--site-header-height, 4rem);"
            @click="close()"
            data-notifications-drawer-overlay></div>

        <aside id="notifications-drawer"
            x-show="open"
            x-cloak
            x-transition:enter="ui-motion-mobile-menu-in"
            x-transition:leave="ui-motion-mobile-menu-out"
            class="fixed inset-x-0 bottom-0 z-[70] flex flex-col overflow-hidden bg-background text-foreground"
            style="top: var(--site-header-height, 4rem);"
            role="dialog"
            aria-modal="true"
            aria-labelledby="notifications-drawer-title"
            aria-describedby="notifications-drawer-description"
            @click.stop
            data-notifications-drawer>
            <header class="ui-card m-2 flex shrink-0 items-start gap-4 bg-muted/50 p-4 sm:m-3 sm:p-6">
                <div class="min-w-0 space-y-1">
                    <h2 id="notifications-drawer-title" class="text-lg leading-none font-semibold tracking-tight">Notifications</h2>
                    <p id="notifications-drawer-description" class="text-sm text-muted-foreground">Updates about your account and league activity.</p>
                </div>
            </header>

            <div class="min-h-0 flex-1 overflow-y-auto p-4" data-notifications-links>
                <div x-show="$store.headerNotifications.loading && ! $store.headerNotifications.initialized"
                    x-cloak
                    class="space-y-3"
                    data-notifications-skeleton>
                    @foreach (range(1, 3) as $skeleton)
                        <div class="flex items-start gap-3 rounded-xl border border-border/70 bg-muted/40 p-4">
                            <div class="size-9 shrink-0 animate-pulse rounded-lg bg-muted"></div>
                            <div class="min-w-0 flex-1 space-y-2">
                                <div class="h-4 w-2/3 animate-pulse rounded bg-muted"></div>
                                <div class="h-3 w-full animate-pulse rounded bg-muted"></div>
                                <div class="h-3 w-1/3 animate-pulse rounded bg-muted"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div x-show="! $store.headerNotifications.loading && $store.headerNotifications.initialized && $store.headerNotifications.notifications.length === 0"
                    x-cloak
                    class="flex min-h-64 flex-col items-center justify-center gap-3 px-6 text-center"
                    data-notifications-empty>
                    <span class="inline-flex size-10 items-center justify-center rounded-lg border border-border/70 bg-muted/50 text-muted-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                        </svg>
                    </span>
                    <div class="space-y-1">
                        <p class="text-sm font-medium">You're all caught up.</p>
                        <p class="text-sm text-muted-foreground">New match reminders, result updates, and knockout activity will appear here.</p>
                    </div>
                </div>

                <div x-show="$store.headerNotifications.notifications.length > 0"
                    x-cloak
                    class="space-y-3"
                    data-notifications-list>
                    <template x-for="notification in $store.headerNotifications.notifications" :key="notification.id">
                        <a :href="notification.open_url"
                            class="group flex flex-col gap-2 rounded-xl border border-border/70 bg-muted/40 p-4 text-foreground outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring/50"
                            :class="! notification.read ? 'bg-muted/80' : ''"
                            :aria-label="notification.title || 'Notification'">
                            <span class="min-w-0 flex-1 space-y-1">
                                <span class="flex items-start justify-between gap-3">
                                    <span class="min-w-0 text-sm leading-5 font-medium" x-text="notification.title || 'Notification'"></span>
                                    <span x-show="! notification.read" class="mt-1.5 size-1.5 shrink-0 rounded-full bg-red-600" aria-label="Unread"></span>
                                </span>
                                <span x-show="notification.body" class="block text-sm leading-5 text-muted-foreground group-hover:text-accent-foreground/80" x-text="notification.body"></span>
                                <span class="block text-xs text-muted-foreground group-hover:text-accent-foreground/70" x-text="notification.created_at_human"></span>
                            </span>
                        </a>
                    </template>
                </div>
            </div>

            <footer class="mt-auto flex shrink-0 flex-col gap-2 border-t border-border/70 bg-muted/50 p-4">
                <button type="button"
                    class="inline-flex h-10 w-full shrink-0 items-center justify-center rounded-full bg-foreground px-4 text-sm font-medium text-background outline-none transition-colors hover:bg-foreground/90 focus-visible:ring-2 focus-visible:ring-ring/50 disabled:pointer-events-none disabled:opacity-50"
                    @click="$store.headerNotifications.markAllAsRead()"
                    :disabled="$store.headerNotifications.markingAll || $store.headerNotifications.unreadCount === 0"
                    data-notifications-mark-all>
                    <span x-show="! $store.headerNotifications.markingAll">Mark all as read</span>
                    <span x-show="$store.headerNotifications.markingAll" x-cloak>Updating...</span>
                </button>
            </footer>
        </aside>
    </div>
@endauth
