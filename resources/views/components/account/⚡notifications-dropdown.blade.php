<div class="relative"
    x-data="{
        id: 'notifications',
        open: false,
        prefersTap() {
            return window.matchMedia('(hover: none), (pointer: coarse)').matches;
        },
        init() {
            this.$store.headerNotifications.configure({
                summaryUrl: @js(route('account.notifications.summary')),
                readAllUrl: @js(route('account.notifications.read-all')),
                readUrlTemplate: @js(route('account.notifications.read', ['notification' => '__NOTIFICATION__'])),
            });
        },
        show() {
            this.open = true;
            this.$store.headerNotifications.refresh();
            this.$dispatch('nav-dropdown-open', { id: this.id });
        },
        openOnHover() {
            if (! this.prefersTap()) {
                this.show();
            }
        },
        closeOnHover() {
            if (! this.prefersTap()) {
                this.open = false;
            }
        },
        toggle() {
            if (this.open) {
                this.open = false;

                return;
            }

            this.show();
        },
    }"
    x-init="init()"
    @mouseenter="openOnHover()"
    @mouseleave="closeOnHover()"
    @click.away="open = false"
    @close.stop="open = false"
    @nav-dropdown-open.window="if ($event.detail.id !== id) open = false">
    <button type="button"
        class="relative inline-flex size-10 items-center justify-center rounded-full text-neutral-700 transition hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800"
        aria-expanded="false"
        aria-label="Open notifications menu"
        @click="toggle()"
        :aria-expanded="open">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-neutral-700 dark:text-neutral-300" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 0 0-12 0v.05c0 .237 0 .476-.003.713A8.967 8.967 0 0 1 3.69 15.772a23.852 23.852 0 0 0 5.454 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        <template x-if="$store.headerNotifications.unreadCount > 0">
            <span
                class="absolute right-2 top-2 inline-flex size-2.5 rounded-full bg-green-600 ring-2 ring-white dark:ring-neutral-950"
                data-unread-notifications-badge
                aria-hidden="true"></span>
        </template>
        <span class="sr-only" x-text="`${$store.headerNotifications.unreadCount} unread notifications`"></span>
    </button>

    <div class="absolute right-0 top-full z-10 mt-3 w-80"
        x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1">
        <div class="ui-card overflow-hidden">
            <div class="ui-card-row justify-between px-4 ring-1 ring-inset ring-gray-200 sm:px-5 dark:ring-neutral-800">
                <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Notifications</p>
                <template x-if="$store.headerNotifications.unreadCount > 0">
                    <button type="button"
                        class="text-xs font-medium text-neutral-500 underline underline-offset-2 transition hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                        @click="$store.headerNotifications.markAllAsRead()">
                        Mark all as read
                    </button>
                </template>
            </div>

            <div class="ui-card-rows max-h-96 overflow-y-auto">
                <template x-if="$store.headerNotifications.notifications.length === 0">
                    <div class="ui-card-row px-4 py-5 text-sm text-neutral-500 dark:text-neutral-400 sm:px-5">
                        You're all caught up. Match reminders, result updates, and knockout activity will show up here when something needs your attention.
                    </div>
                </template>

                <template x-for="notification in $store.headerNotifications.notifications" :key="notification.id">
                    <div class="group relative transition sm:hover:bg-gray-100 dark:sm:hover:bg-neutral-800">
                        <a :href="notification.open_url"
                            class="absolute inset-0 z-0"
                            :aria-label="notification.title || 'Notification'"></a>
                        <div class="ui-card-row px-4 sm:px-5">
                            <div class="relative z-10 min-w-0 flex-1 pointer-events-none">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="min-w-0 text-sm font-semibold text-neutral-900 dark:text-neutral-100" x-text="notification.title || 'Notification'"></p>
                                    <template x-if="! notification.read">
                                        <span class="mt-1 inline-flex size-2.5 shrink-0 rounded-full bg-green-600" aria-hidden="true"></span>
                                    </template>
                                </div>
                                <template x-if="notification.body">
                                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300" x-text="notification.body"></p>
                                </template>
                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400" x-text="notification.created_at_human"></p>

                                    <template x-if="! notification.read">
                                        <button type="button"
                                            class="pointer-events-auto relative z-20 shrink-0 rounded-sm px-1 text-right text-xs font-medium text-neutral-500 underline underline-offset-2 opacity-0 transition hover:text-neutral-700 focus:opacity-100 focus:outline-hidden dark:text-neutral-400 dark:hover:text-neutral-200 group-hover:opacity-100"
                                            @click.prevent="$store.headerNotifications.markAsRead(notification.id)">
                                            Mark as read
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
