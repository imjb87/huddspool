<footer class="bg-neutral-100 dark:bg-neutral-950">
    @if ($is_impersonating ?? false)
        <div class="border-b border-gray-200 dark:border-neutral-800/80">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="flex justify-end py-3">
                    <a href="{{ route('impersonation.leave') }}"
                        class="ui-link text-xs">
                        Stop impersonating
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="mx-auto flex max-w-6xl items-center justify-center px-6 py-8 text-center">
        <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
            Built by
            <a href="mailto:john@thebiggerboat.co.uk" class="underline decoration-neutral-300 underline-offset-4 transition-colors hover:text-neutral-900 dark:decoration-neutral-700 dark:hover:text-neutral-100">
                John Bell
            </a>
            at
            <a href="https://www.thebiggerboat.co.uk/" target="_blank" rel="noopener noreferrer" class="underline decoration-neutral-300 underline-offset-4 transition-colors hover:text-neutral-900 dark:decoration-neutral-700 dark:hover:text-neutral-100">The Bigger Boat</a>.
        </p>
    </div>
</footer>
