<div class="grid gap-6" data-account-team-page>
    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Account</p>
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">Your team</h1>
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
