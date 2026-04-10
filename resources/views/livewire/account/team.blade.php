<div class="grid gap-6" data-account-team-page>
    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <div class="ui-page-title-with-icon">
                        <div class="ui-page-title-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.742-.478 3 3 0 0 0-4.682-2.72m.94 3.198v-.75A2.25 2.25 0 0 0 15.75 15.72h-7.5A2.25 2.25 0 0 0 6 17.97v.75m12 0a9.094 9.094 0 0 1-12 0m12 0a9.094 9.094 0 0 0-12 0m8.25-10.47a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Account</p>
                            <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Your team</h1>
                        </div>
                    </div>
                </div>

                <div aria-hidden="true"></div>
            </div>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white dark:border-neutral-800/80 dark:bg-neutral-900/75" data-account-nav>
        <div class="ui-tab-strip-shell">
            <nav class="ui-tab-strip">
                <a href="{{ route('account.show') }}" class="ui-button-secondary shrink-0">
                    Profile
                </a>
                <a href="{{ route('account.team') }}" class="ui-button-primary shrink-0">
                    Team
                </a>
                <a href="{{ route('support.tickets') }}" class="ui-button-secondary shrink-0">
                    Support
                </a>
            </nav>
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-6">
            @include('livewire.account.partials.action-centre')
            @include('livewire.account.team-partials.info-section')
            <livewire:team.players-section :team="$this->team" :section="$this->currentSection" :for-account="true" :key="'account-team-players-'.$this->team->id" />
            <livewire:team.fixtures-section :team="$this->team" :section="$this->currentSection" :for-account="true" :key="'account-team-fixtures-'.$this->team->id" />
            @include('livewire.account.team-partials.knockout-section')
        </div>
    </div>
</div>
