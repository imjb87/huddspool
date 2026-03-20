@if (! $result->is_confirmed)
    <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 dark:border-yellow-900/60 dark:bg-yellow-950/30">
        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
            This result is still in progress and will remain editable until it is locked.
        </p>
    </div>
@endif

@if ($result->is_overridden)
    <div class="px-4 py-10 text-center sm:px-6">
        <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result overridden</h3>
            <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                This match result was overridden by an admin.
            </p>
        </div>
    </div>
@endif
