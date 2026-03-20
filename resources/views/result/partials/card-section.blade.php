<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-result-card-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Frame-by-frame scores, match totals, and submission details.
            </p>
        </div>

        <div class="space-y-5 lg:col-span-2">
            @include('result.partials.status-banner')

            @if (! $result->is_overridden)
                <div class="space-y-4" data-result-card-shell>
                    @include('result.partials.frames-list')
                    @include('result.partials.match-total')
                </div>
            @endif

            @include('result.partials.submitted-by')
        </div>
    </div>
</section>
