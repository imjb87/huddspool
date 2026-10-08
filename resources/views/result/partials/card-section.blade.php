<section class="ui-section" data-result-card-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-check size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2" />
                    <path d="M9 14l2 2l4 -4" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Result card</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    Review each frame, the final score, and the submitted scorecard.
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

                <div class="flex justify-end pt-3" data-result-card-share
                    x-data="{
                        shareUrl: @js($shareUrl),
                        shareTitle: @js($shareTitle),
                        async shareCard() {
                            if (navigator.share) {
                                try {
                                    await navigator.share({
                                        title: this.shareTitle,
                                        text: 'View the shared result card',
                                        url: this.shareUrl,
                                    });
                                    return;
                                } catch (error) {
                                    if (error?.name === 'AbortError') {
                                        return;
                                    }
                                }
                            }

                            if (navigator.clipboard?.writeText) {
                                await navigator.clipboard.writeText(this.shareUrl);
                                return;
                            }
                        },
                    }">
                    <button type="button"
                        class="ui-button-secondary gap-2"
                        x-on:click="shareCard()"
                        data-result-share-card-button>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-share-3 size-4" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M13 4v4c-6.575 1.028 -9.02 6.788 -10 12c-.037 .206 5.384 -5.962 10 -6v4l8 -7l-8 -7" />
                        </svg>
                        <span>Share result</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</section>
