<section id="account-profile" class="ui-section" data-account-profile-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Personal information</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Manage the profile details shown for your account and keep your avatar up to date.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body space-y-5">
                    <div class="space-y-5">
                        <div class="flex items-start gap-4 sm:gap-6">
                            <div class="shrink-0">
                            @if ($removeAvatar)
                                <img src="{{ asset('/images/user.jpg') }}"
                                    alt="Avatar preview"
                                    class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-neutral-800/80">
                            @elseif ($avatarUpload)
                                <img src="{{ $avatarUpload->temporaryUrl() }}"
                                    alt="Avatar preview"
                                    class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-neutral-800/80">
                            @else
                                <img src="{{ $this->user->avatar_url }}"
                                    alt="{{ $this->user->name }} avatar"
                                    class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-neutral-800/80">
                            @endif
                            </div>

                            <div class="min-w-0 flex-1 space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <label class="ui-button-secondary cursor-pointer">
                                        <span>Change avatar</span>
                                        <input type="file" wire:model="avatarUpload" class="hidden" accept="image/*">
                                    </label>

                                    @if ($this->user->avatar_path || $avatarUpload)
                                        <button type="button"
                                            wire:click="clearAvatar"
                                            class="ui-link text-sm font-medium">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or GIF up to 5MB.</p>

                                @error('avatarUpload')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</p>
                                <a href="{{ route('player.show', $this->user) }}"
                                    class="ui-link inline-flex max-w-full text-sm font-semibold">
                                    <span>{{ $this->user->name }}</span>
                                </a>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Role</p>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $this->user->roleLabel() }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Team</p>
                                @if ($this->team)
                                    <a href="{{ route('team.show', $this->team) }}"
                                        class="ui-link inline-flex max-w-full text-sm font-semibold">
                                        <span>{{ $this->team->name }}</span>
                                    </a>
                                @else
                                    <p class="text-sm text-gray-900 dark:text-gray-100">Free agent</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card-rows">
                    <div class="block border-t border-gray-200 dark:border-neutral-800/75">
                        <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-neutral-800/75">
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                <p class="mt-1 text-center text-base font-semibold text-gray-900 dark:text-gray-100">{{ $this->record->frames_played }}</p>
                            </div>
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                <div class="mt-1 flex items-center justify-center gap-2">
                                    <p class="text-base font-semibold text-green-700 dark:text-green-400">{{ $this->record->frames_won }}</p>
                                    <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">
                                        {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($this->record->frames_won_percentage) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                <div class="mt-1 flex items-center justify-center gap-2">
                                    <p class="text-base font-semibold text-red-700 dark:text-red-400">{{ $this->record->frames_lost }}</p>
                                    <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">
                                        {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($this->record->frames_lost_percentage) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-4 sm:px-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="account-email" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Email address</label>
                                <input type="email"
                                    id="account-email"
                                    wire:model.live="email"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-100">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="account-telephone" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Phone number</label>
                                <input type="text"
                                    id="account-telephone"
                                    wire:model.live="telephone"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-neutral-800 dark:bg-neutral-950 dark:text-gray-100">
                                @error('telephone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-5 border-t border-gray-200 pt-5 dark:border-neutral-800/75"
                            x-data="pushNotificationsPanel({
                                configured: @js($this->webPushConfigured),
                                enabled: @js($this->hasPushSubscriptions),
                                publicKey: @js(config('services.web_push.public_key')),
                                subscribeUrl: @js(route('account.push-subscriptions.store')),
                                unsubscribeUrl: @js(route('account.push-subscriptions.destroy')),
                            })"
                            x-init="init()"
                            data-account-push-settings>
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">Push notifications</p>
                                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300" x-show="configured && supported && enabled">
                                        You will receive live reminders from Huddspool on your device.
                                    </p>
                                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300" x-show="configured && supported && !enabled">
                                        Enable this to receive live reminders on your device.
                                    </p>
                                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300" x-show="configured && !supported">
                                        This browser does not support push notifications.
                                    </p>
                                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300" x-show="!configured">
                                        Push notifications are not configured on the server yet.
                                    </p>
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400" x-show="error" x-text="error"></p>
                                </div>

                                <div class="flex shrink-0 items-center">
                                    <button type="button"
                                        role="switch"
                                        aria-label="Toggle push notifications"
                                        x-bind:aria-checked="enabled ? 'true' : 'false'"
                                        x-bind:disabled="busy || !configured || !supported"
                                        @click="enabled ? disable() : enable()"
                                        class="group relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition focus:outline-hidden focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-neutral-900"
                                        :class="enabled
                                            ? 'bg-green-700 dark:bg-green-600'
                                            : 'bg-gray-300 dark:bg-neutral-700'">
                                        <span class="sr-only">Toggle push notifications</span>
                                        <span aria-hidden="true"
                                            class="inline-block h-5 w-5 rounded-full bg-white shadow-sm ring-1 ring-black/5 transition duration-200 ease-out dark:bg-neutral-100"
                                            :class="enabled ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card-footer flex items-center justify-end">
                    <button type="button"
                        wire:click="saveProfile"
                        class="ui-button-primary min-w-24">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
