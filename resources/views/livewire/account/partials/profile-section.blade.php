<section id="account-profile" class="ui-section" data-account-profile-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Personal information</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Manage the profile details shown for your account and keep your avatar up to date.
            </p>
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
                                    class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700/80">
                            @elseif ($avatarUpload)
                                <img src="{{ $avatarUpload->temporaryUrl() }}"
                                    alt="Avatar preview"
                                    class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700/80">
                            @else
                                <img src="{{ $this->user->avatar_url }}"
                                    alt="{{ $this->user->name }} avatar"
                                    class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700/80">
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
                    <div class="block border-t border-gray-200 dark:border-zinc-700/75">
                        <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-zinc-700/75">
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
                                    class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="account-telephone" class="block text-xs font-medium text-gray-500 dark:text-gray-400">Phone number</label>
                                <input type="text"
                                    id="account-telephone"
                                    wire:model.live="telephone"
                                    class="mt-1 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                                @error('telephone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
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
