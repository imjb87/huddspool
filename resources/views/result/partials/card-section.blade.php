<section class="ui-section" data-result-card-section>
    <div class="ui-shell-grid">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Frame-by-frame scores, match totals, and submission details.
            </p>
        </div>

        <div class="lg:col-span-2">
            @include('result.partials.status-banner')

            @if (! $result->is_overridden)
                <div class="ui-card" data-result-card-shell>
                    @include('result.partials.frames-list')
                    @include('result.partials.match-total')
                    @include('result.partials.submitted-by')
                </div>
            @endif
        </div>
    </div>
</section>
