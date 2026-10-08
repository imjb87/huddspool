@if ($result->is_overridden)
    <div class="ui-card" data-result-overridden-card>
        <div class="ui-card-body">
            <div class="rounded-lg border border-red-200/70 bg-red-50/50 px-4 py-4 dark:border-red-900/70 dark:bg-red-950/30">
                <h3 class="text-sm font-semibold text-red-900 dark:text-red-100">Result overridden</h3>
                <p class="mt-1 max-w-prose text-sm leading-5 text-red-800/80 dark:text-red-200/80">
                    This match result was overridden by an admin.
                </p>
            </div>
        </div>
    </div>
@endif
