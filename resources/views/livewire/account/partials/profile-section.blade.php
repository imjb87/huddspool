<section id="account-profile" class="py-1" data-account-profile-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Personal Information</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Manage the profile details shown for your account and keep your avatar up to date.
            </p>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="space-y-3">
                <div class="flex items-end justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="shrink-0">
                            @if ($removeAvatar)
                                <img src="{{ asset('/images/user.jpg') }}"
                                    alt="Avatar preview"
                                    class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700">
                            @elseif ($avatarUpload)
                                <img src="{{ $avatarUpload->temporaryUrl() }}"
                                    alt="Avatar preview"
                                    class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700">
                            @else
                                <img src="{{ $this->user->avatar_url }}"
                                    alt="{{ $this->user->name }} avatar"
                                    class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-zinc-700">
                            @endif
                        </div>

                        <div class="space-y-1.5">
                            <label class="inline-flex cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-200 hover:text-gray-900 dark:border-zinc-700 dark:bg-zinc-700 dark:text-gray-300 dark:hover:border-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-gray-100">
                                <span>Change avatar</span>
                                <input type="file" wire:model="avatarUpload" class="hidden" accept="image/*">
                            </label>

                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or GIF up to 5MB.</p>
                        </div>
                    </div>

                    @if ($this->user->avatar_path || $avatarUpload)
                        <button type="button"
                            wire:click="clearAvatar"
                            class="shrink-0 self-end text-sm font-medium text-gray-600 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-400 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            Remove
                        </button>
                    @endif
                </div>
            </div>

            @error('avatarUpload')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <div class="pt-1">
                <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</p>
                        <a href="{{ route('player.show', $this->user) }}"
                            class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                            {{ $this->user->name }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Team</p>
                        @if ($this->team)
                            <a href="{{ route('team.show', $this->team) }}"
                                class="mt-2 inline-flex text-sm font-semibold text-gray-700 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-zinc-600 dark:hover:text-gray-100 dark:hover:decoration-zinc-400">
                                {{ $this->team->name }}
                            </a>
                        @else
                            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Free agent</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $this->user->roleLabel() }}</p>
                    </div>

                    <div class="pt-2 pb-2 sm:col-span-2">
                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-800/75 dark:shadow-none dark:ring-1 dark:ring-zinc-700/80">
                            <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-zinc-700">
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Played</p>
                                    <p class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $this->record->frames_played }}</p>
                                </div>
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Won</p>
                                    <div class="mt-1 flex items-end justify-between gap-2">
                                        <p class="text-base font-semibold text-green-700 dark:text-green-400">{{ $this->record->frames_won }}</p>
                                        <span class="inline-flex shrink-0 items-center rounded-md bg-green-100 px-1.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-300">
                                            {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($this->record->frames_won_percentage) }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="px-4 py-4 sm:px-5">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lost</p>
                                    <div class="mt-1 flex items-end justify-between gap-2">
                                        <p class="text-base font-semibold text-red-700 dark:text-red-400">{{ $this->record->frames_lost }}</p>
                                        <span class="inline-flex shrink-0 items-center rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-950/50 dark:text-red-300">
                                            {{ \App\Support\PercentageFormatter::wholeOrSingleDecimal($this->record->frames_lost_percentage) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="account-email" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Email address</label>
                        <input type="email"
                            id="account-email"
                            wire:model.live="email"
                            class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="account-telephone" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Phone number</label>
                        <input type="text"
                            id="account-telephone"
                            wire:model.live="telephone"
                            class="mt-2 block w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-green-700 focus:outline-hidden focus:ring-2 focus:ring-green-700/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100">
                        @error('telephone')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <button type="button"
                        wire:click="saveProfile"
                        class="inline-flex min-w-24 items-center justify-center rounded-full bg-linear-to-br from-green-900 via-green-800 to-green-700 px-4 py-2 text-sm font-medium text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
