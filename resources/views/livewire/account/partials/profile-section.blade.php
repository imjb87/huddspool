<section id="account-profile" class="ui-section" data-account-profile-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Personal information</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Update the name, role, team, and avatar shown on your profile.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-body space-y-5">
                    <div class="space-y-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="shrink-0">
                                @if ($removeAvatar)
                                    <img src="{{ asset('/images/user.jpg') }}"
                                        alt="Avatar preview"
                                        class="h-20 w-20 rounded-full object-cover ring-1 ring-foreground/10">
                                @elseif ($avatarUpload)
                                    <img src="{{ $avatarUpload->temporaryUrl() }}"
                                        alt="Avatar preview"
                                        class="h-20 w-20 rounded-full object-cover ring-1 ring-foreground/10">
                                @else
                                    <img src="{{ $this->user->avatar_url }}"
                                        alt="{{ $this->user->name }} avatar"
                                        class="h-20 w-20 rounded-full object-cover ring-1 ring-foreground/10">
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 space-y-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <label class="ui-result-button ui-result-button-primary cursor-pointer">
                                        <span>Change avatar</span>
                                        <input type="file" wire:model="avatarUpload" class="hidden" accept="image/*">
                                    </label>

                                    @if ($this->user->avatar_path || $avatarUpload)
                                        <button type="button"
                                            wire:click="clearAvatar"
                                            class="text-sm font-medium text-muted-foreground underline decoration-border underline-offset-4 transition-colors hover:text-foreground">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                <p class="text-xs text-muted-foreground">PNG, JPG or GIF up to 5MB.</p>

                                @error('avatarUpload')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div class="col-span-full min-w-0 sm:col-span-1">
                                <p class="text-xs font-medium text-muted-foreground">Name</p>
                                <a href="{{ route('player.show', $this->user) }}"
                                    class="ui-link flex w-full max-w-full text-sm font-semibold">
                                    <span class="block w-full break-words">{{ $this->user->name }}</span>
                                </a>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Role</p>
                                <p class="text-sm text-foreground">{{ $this->user->roleLabel() }}</p>
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-xs font-medium text-muted-foreground">Team</p>
                                @if ($this->team)
                                    <a href="{{ route('team.show', $this->team) }}"
                                        class="ui-link inline-flex max-w-full text-sm font-semibold">
                                        <span>{{ $this->team->name }}</span>
                                    </a>
                                @else
                                    <p class="text-sm text-foreground">Free agent</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card-rows">
                    <div class="block border-t border-border">
                        <div class="grid grid-cols-3 divide-x divide-border">
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-center text-xs font-medium text-muted-foreground">Played</p>
                                <p class="mt-1 text-center text-base font-semibold text-foreground">{{ $this->record->frames_played }}</p>
                            </div>
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-center text-xs font-medium text-muted-foreground">Won</p>
                                <div class="mt-1 flex items-center justify-center gap-2">
                                    <p class="text-base font-semibold text-green-700 dark:text-green-400">{{ $this->record->frames_won }}</p>
                                    <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-500/15 dark:text-green-300">
                                        {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($this->record->frames_won_percentage) }}%
                                    </span>
                                </div>
                            </div>
                            <div class="px-4 py-4 sm:px-5">
                                <p class="text-center text-xs font-medium text-muted-foreground">Lost</p>
                                <div class="mt-1 flex items-center justify-center gap-2">
                                    <p class="text-base font-semibold text-red-700 dark:text-red-400">{{ $this->record->frames_lost }}</p>
                                    <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/15 dark:text-red-300">
                                        {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($this->record->frames_lost_percentage) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 border-t border-border px-5 py-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="account-email" class="block text-sm font-medium text-foreground">Email address</label>
                                <input type="email"
                                    id="account-email"
                                    wire:model.live="email"
                                    class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="account-telephone" class="block text-sm font-medium text-foreground">Phone number</label>
                                <input type="text"
                                    id="account-telephone"
                                    wire:model.live="telephone"
                                    class="mt-2 block h-9 w-full rounded-md border border-border bg-background px-3 py-1 text-sm text-foreground shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50">
                                @error('telephone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="border-t border-border pt-5"
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
                                    <p class="text-sm font-medium text-foreground">Push notifications</p>
                                    <p class="mt-1 text-sm leading-5 text-muted-foreground" x-show="configured && supported && enabled">
                                        You will receive live reminders from Huddspool on your device.
                                    </p>
                                    <p class="mt-1 text-sm leading-5 text-muted-foreground" x-show="configured && supported && !enabled">
                                        Enable this to receive live reminders on your device.
                                    </p>
                                    <p class="mt-1 text-sm leading-5 text-muted-foreground" x-show="configured && !supported">
                                        This browser does not support push notifications.
                                    </p>
                                    <p class="mt-1 text-sm leading-5 text-muted-foreground" x-show="!configured">
                                        Push notifications aren't available for this account yet.
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
                                        class="group relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition focus:outline-hidden focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50"
                                        :class="enabled
                                            ? 'bg-foreground'
                                            : 'bg-muted-foreground/40'">
                                        <span class="sr-only">Toggle push notifications</span>
                                        <span aria-hidden="true"
                                            class="inline-block size-5 rounded-full bg-background shadow-sm ring-1 ring-foreground/10 transition duration-200 ease-out"
                                            :class="enabled ? 'translate-x-5' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card-footer flex items-center justify-end">
                    <button type="button"
                        wire:click="saveProfile"
                        class="ui-result-button ui-result-button-primary min-w-24">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
