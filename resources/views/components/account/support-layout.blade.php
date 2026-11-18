<div {{ $attributes->merge(['class' => 'ui-page-shell']) }}>
    <div class="ui-section" data-section-shared-header data-account-header>
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">Your account</h1>
        </div>
    </div>

    <section class="bg-card text-card-foreground ring-1 ring-foreground/10" data-account-nav>
        <div class="ui-tab-strip-shell">
            <nav class="ui-tab-strip" aria-label="Account navigation">
                <a href="{{ route('account.show') }}" class="ui-tab-trigger shrink-0" data-state="inactive">Account</a>
                <a href="{{ route('support.tickets') }}" class="ui-tab-trigger shrink-0" data-state="active" aria-current="page">Support</a>
            </nav>
        </div>
    </section>

    <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        {{ $slot }}
    </div>
</div>
