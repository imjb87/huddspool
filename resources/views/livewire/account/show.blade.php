    <div class="grid gap-6">
        @if ($this->resultSubmissionPrompt)
            @include('livewire.account.partials.result-submission-prompt', ['resultSubmissionPrompt' => $this->resultSubmissionPrompt])
        @endif

    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Account</p>
                    <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Your profile</h1>
                </div>

                <div aria-hidden="true"></div>
            </div>
        </div>
    </div>

    <section class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75" data-account-nav>
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="flex gap-2">
                <a href="{{ route('account.show') }}" class="ui-button-primary shrink-0">
                    Profile
                </a>

                @if ($this->user->can(\App\Enums\PermissionName::SubmitLeagueResults->value))
                    <a href="{{ route('account.team') }}" class="ui-button-secondary shrink-0">
                        Team
                    </a>
                @endif

                <a href="{{ route('support.tickets') }}" class="ui-button-secondary shrink-0">
                    Support
                </a>
            </nav>
        </div>
    </section>

    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="space-y-6">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            @include('livewire.account.partials.profile-section')
            @include('livewire.account.partials.knockout-section')
            <livewire:player.frames-section :player="$this->user" :section="$this->currentSection" :for-account="true" :key="'account-frames-'.$this->user->id" />
            @include('livewire.account.partials.history-section')
        </div>
    </div>
</div>
