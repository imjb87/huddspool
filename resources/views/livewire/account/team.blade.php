<div class="pb-10 lg:pb-14 dark:bg-zinc-900" data-account-team-page>
    @if ($this->resultSubmissionPrompt)
        <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200" data-account-result-submission-prompt>
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)] md:items-start md:gap-6">
                    <div class="min-w-0">
                        <p class="font-medium">{{ $this->resultSubmissionPrompt->message }}</p>
                        <p class="mt-1 text-xs text-red-800 dark:text-red-300">{{ $this->resultSubmissionPrompt->fixture_label }}</p>
                    </div>

                    @if (count($this->resultSubmissionPrompt->fixtures ?? []) > 1)
                        <div class="space-y-1.5 md:pt-0.5">
                            @foreach ($this->resultSubmissionPrompt->fixtures as $fixturePrompt)
                                <a href="{{ $fixturePrompt['url'] }}"
                                    class="block text-sm font-medium text-red-800 underline decoration-red-300 underline-offset-3 transition hover:text-red-900 hover:decoration-red-500 dark:text-red-200 dark:decoration-red-800 dark:hover:text-red-100 dark:hover:decoration-red-600">
                                    {{ $fixturePrompt['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="md:flex md:justify-end">
                            <a href="{{ $this->resultSubmissionPrompt->url }}"
                                class="inline-flex h-8 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                {{ $this->resultSubmissionPrompt->button_label ?? 'Submit result' }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
        data-section-shared-header
        data-account-header>
        <div class="min-w-0">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your team</h1>
        </div>
    </div>

    <div class="border-y border-gray-200 bg-white dark:border-zinc-800/80 dark:bg-zinc-800/75" data-account-nav>
        <div class="mx-auto flex w-full max-w-4xl gap-2 overflow-x-auto px-4 py-3 sm:px-6 lg:px-6">
            <nav class="-ml-3 flex gap-2">
                <a href="{{ route('account.show') }}"
                    class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-800/70 dark:hover:text-gray-100">
                    Profile
                </a>
                <a href="{{ route('account.team') }}"
                    class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 transition dark:bg-zinc-700 dark:text-gray-300">
                    Team
                </a>
                <a href="{{ route('support.tickets') }}"
                    class="inline-flex shrink-0 items-center rounded-full px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-zinc-800/70 dark:hover:text-gray-100">
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
            @include('livewire.account.team-partials.info-section')
            @include('livewire.account.team-partials.members-section')
            @include('livewire.account.team-partials.fixtures-section')
            @include('livewire.account.team-partials.knockout-section')
        </div>
    </div>
</div>
