<footer class="bg-white dark:border-t dark:border-zinc-800/80 dark:bg-zinc-900/95">
    @if (app('impersonate')->isImpersonating())
        <div class="border-b border-gray-200 dark:border-zinc-800/80">
            <div class="mx-auto max-w-7xl px-6 py-3 text-right lg:px-8">
                <a href="{{ route('impersonation.leave') }}"
                    class="text-xs font-semibold text-gray-600 underline decoration-gray-300 underline-offset-3 transition hover:text-gray-900 hover:decoration-gray-500 dark:text-gray-300 dark:decoration-gray-500 dark:hover:text-white dark:hover:decoration-gray-300">
                    Stop impersonating
                </a>
            </div>
        </div>
    @endif
    <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
        <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
            &copy; 2023 Huddersfield & District Tuesday Night Pool League.
        </p>
        <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
            Website built by John Bell.
        </p>
    </div>
</footer>
