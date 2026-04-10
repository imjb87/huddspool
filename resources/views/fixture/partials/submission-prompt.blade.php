@if (filled($submissionUrl))
    <section class="ui-section" data-fixture-submission-prompt>
        <div class="ui-shell-grid">
            <div class="ui-section-intro">
                <div class="ui-section-intro-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 6.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="ui-section-intro-copy">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Result submission</h3>
                    <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                        This fixture is ready for a result to be submitted.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-card border-red-200 bg-red-50 dark:border-red-900/60 dark:bg-red-950/20">
                    <div class="ui-card-rows">
                        <a href="{{ $submissionUrl }}" class="ui-card-row-link">
                            <span class="sr-only">Submit result</span>
                            <div class="ui-card-row items-start px-4 sm:items-center sm:px-5">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-200">
                                        {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-red-700 dark:text-red-300">
                                        Date: {{ $fixture->fixture_date?->format('j M Y \\a\\t 20:00') ?? 'TBC' }}
                                    </p>
                                </div>

                                <div class="ml-auto flex shrink-0 self-center text-right">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-red-700 dark:text-red-300" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5 15.75 12l-7.5 7.5" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
