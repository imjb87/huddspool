    <div class="grid gap-6">
    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    <div class="ui-page-title-with-icon">
                        <div class="ui-page-title-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Account</p>
                            <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">Your account</h1>
                        </div>
                    </div>
                </div>

                <div aria-hidden="true"></div>
            </div>
        </div>
    </div>

    <section class="border-y border-gray-200 bg-white dark:border-neutral-800/80 dark:bg-neutral-900/75" data-account-nav>
        <div class="ui-tab-strip-shell">
            <nav class="ui-tab-strip">
                <a href="{{ route('account.show') }}" class="ui-button-primary shrink-0">
                    Account
                </a>
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

            @include('livewire.account.partials.action-centre')
            @include('livewire.account.partials.profile-section')
        </div>
    </div>
</div>
