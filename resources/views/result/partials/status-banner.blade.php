@if ($result->is_overridden)
    <div class="ui-card">
        <div class="ui-card-body py-10 text-center">
            <div class="mx-auto max-w-md rounded-xl border border-dashed border-gray-300 px-6 py-8 dark:border-zinc-700 dark:bg-zinc-800/75">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result overridden</h3>
                <p class="mx-auto mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                    This match result was overridden by an admin.
                </p>
            </div>
        </div>
    </div>
@endif
