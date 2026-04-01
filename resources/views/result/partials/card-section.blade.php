<section class="ui-section" data-result-card-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M9 5.25H7.5A2.25 2.25 0 0 0 5.25 7.5v9A2.25 2.25 0 0 0 7.5 18.75h9A2.25 2.25 0 0 0 18.75 16.5v-9A2.25 2.25 0 0 0 16.5 5.25H15m-6 0V3.75A.75.75 0 0 1 9.75 3h4.5a.75.75 0 0 1 .75.75v1.5m-6 0h6" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result card</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Frame-by-frame scores, match totals, and submission details.
                </p>
            </div>
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
