<div class="grid gap-6" data-account-team-page>
    @if ($this->resultSubmissionPrompt)
        @include('livewire.account.partials.result-submission-prompt', ['resultSubmissionPrompt' => $this->resultSubmissionPrompt])
    @endif

    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Account</p>
                    <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Your team</h1>
                </div>

                <div aria-hidden="true"></div>
            </div>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75" data-account-nav>
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="flex gap-2">
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
            @include('livewire.account.team-partials.info-section')
            <livewire:team.players-section :team="$this->team" :section="$this->currentSection" :for-account="true" :key="'account-team-players-'.$this->team->id" />
            <livewire:team.fixtures-section :team="$this->team" :section="$this->currentSection" :for-account="true" :key="'account-team-fixtures-'.$this->team->id" />
            @include('livewire.account.team-partials.knockout-section')
        </div>
    </div>
</div>
