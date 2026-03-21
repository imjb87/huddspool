<div class="pb-10 lg:pb-14 dark:bg-zinc-900" data-account-page>
    @if ($this->resultSubmissionPrompt)
        @include('livewire.account.partials.result-submission-prompt', ['resultSubmissionPrompt' => $this->resultSubmissionPrompt])
    @endif

    <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
        data-section-shared-header
        data-account-header>
        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your profile</h1>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75" data-account-nav>
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="-ml-3 flex gap-2">
                <a href="{{ route('account.show') }}"
                    class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 transition dark:bg-zinc-700 dark:text-gray-300">
                    Profile
                </a>
                @if ($this->user->can(\App\Enums\PermissionName::SubmitLeagueResults->value))
                    <a href="{{ route('account.team') }}"
                        class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-700 dark:hover:text-gray-100">
                        Team
                    </a>
                @endif
                <a href="{{ route('support.tickets') }}"
                    class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-700 dark:hover:text-gray-100">
                    Support
                </a>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-6">
            @include('livewire.account.partials.profile-section')
            @include('livewire.account.partials.knockout-section')
            @include('livewire.account.partials.frames-section')
            @include('livewire.account.partials.history-section')
        </div>
    </div>
</div>
