<div class="grid gap-6">
    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">Your account</h1>
        </div>
    </div>

    <section class="bg-card text-card-foreground ring-1 ring-foreground/10" data-account-nav>
        <div class="ui-tab-strip-shell">
            <nav class="ui-tab-strip" aria-label="Account navigation">
                <a href="{{ route('account.show') }}"
                    class="ui-tab-trigger shrink-0"
                    data-state="active"
                    aria-current="page">
                    Account
                </a>
                <a href="{{ route('support.tickets') }}" class="ui-tab-trigger shrink-0" data-state="inactive">
                    Support
                </a>
            </nav>
        </div>
    </section>

    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-green-500/50 bg-green-50/50 px-4 py-3.5 text-sm text-green-900 dark:border-green-500/50 dark:bg-green-950/20 dark:text-green-200" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @include('livewire.account.partials.action-centre')
            @include('livewire.account.partials.profile-section')
        </div>
    </div>
</div>
