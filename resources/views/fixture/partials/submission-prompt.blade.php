@if (filled($submissionUrl))
    <section class="ui-section" data-fixture-submission-prompt>
        <div class="ui-shell-grid">
        <div class="ui-section-intro gap-2">
            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-plus size-5 text-neutral-700 dark:text-neutral-200">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2" />
                    <path d="M10 14h4" />
                    <path d="M12 12v4" />
                </svg>
            </span>
            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Result submission</h2>
                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                    This fixture is ready for a result to be submitted.
                </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card" data-fixture-submission-card>
                    <div class="ui-card-body">
                        <a href="{{ $submissionUrl }}" class="group flex items-center gap-4 rounded-lg border border-amber-200/70 bg-amber-50/50 px-4 py-4 outline-none transition-colors hover:bg-amber-100/60 focus-visible:ring-2 focus-visible:ring-amber-700/20 dark:border-amber-900/70 dark:bg-amber-950/30 dark:hover:bg-amber-950/50 dark:focus-visible:ring-amber-200/20">
                            <span class="sr-only">Submit result</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                                    {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}
                                </p>
                                <p class="mt-1 text-xs leading-5 text-amber-800/80 dark:text-amber-200/80">
                                    Date: {{ $fixture->fixture_date?->format('j M Y \\a\\t 20:00') ?? 'TBC' }}
                                </p>
                            </div>

                            <span class="flex shrink-0 items-center justify-center text-amber-800 transition-transform duration-100 group-hover:translate-x-0.5 dark:text-amber-200" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right size-4">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 6l6 6l-6 6" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
