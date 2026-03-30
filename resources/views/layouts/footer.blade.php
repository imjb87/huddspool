<footer class="bg-gray-100 dark:bg-zinc-900">
    @if ($is_impersonating ?? false)
        <div class="border-b border-gray-200 dark:border-zinc-800/80">
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

    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid items-start py-6">
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Huddersfield & District Tuesday Night Pool League
                </p>
                <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Fixtures, results, standings, averages, and team history in one place.
                </p>
            </div>

            <div class="lg:col-span-2 lg:text-right">
                <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">
                    &copy; 2023 Huddersfield & District Tuesday Night Pool League.
                </p>
                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                    Website built by John Bell.
                </p>
            </div>
        </div>
    </div>
</footer>
