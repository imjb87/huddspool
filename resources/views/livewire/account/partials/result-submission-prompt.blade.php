<div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
    <section class="space-y-5 text-sm text-red-900 dark:text-red-200" data-account-result-submission-prompt>
        <div class="space-y-5">
            @if (count($resultSubmissionPrompt->fixtures ?? []) > 0)
                <div class="ui-shell-grid">
                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/80 ring-1 ring-red-200/80 shadow-sm dark:bg-red-950/50 dark:ring-red-900/60">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-red-700 dark:text-red-200" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 6.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-red-950 dark:text-red-50">
                                {{ $resultSubmissionPrompt->fixtures_heading ?? 'League results to submit' }}
                            </h3>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-red-800/90 dark:text-red-300">
                                Outstanding league results assigned to your account.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card overflow-hidden border-red-200/80 bg-transparent ring-1 ring-red-200/80 dark:border-red-900/60 dark:ring-red-900/60">
                            <div class="ui-card-rows divide-red-200/80 dark:divide-red-900/60">
                                @foreach ($resultSubmissionPrompt->fixtures as $fixturePrompt)
                                    <a href="{{ $fixturePrompt['url'] }}"
                                        class="block">
                                        <div
                                            class="ui-card-row items-start bg-linear-to-r from-red-50 via-red-50/95 to-amber-50/70 px-4 py-3 text-red-900 transition hover:bg-linear-to-r hover:from-red-100 hover:via-red-50 hover:to-amber-100 dark:from-red-950/40 dark:via-red-950/30 dark:to-amber-950/10 dark:text-red-200 dark:hover:from-red-950/55 dark:hover:via-red-950/40 dark:hover:to-amber-950/20">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-red-900 dark:text-red-100">
                                                    {{ $fixturePrompt['label'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (count($resultSubmissionPrompt->knockouts ?? []) > 0)
                <div class="ui-shell-grid">
                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/80 ring-1 ring-red-200/80 shadow-sm dark:bg-red-950/50 dark:ring-red-900/60">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-red-700 dark:text-red-200" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 6.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-red-950 dark:text-red-50">
                                {{ $resultSubmissionPrompt->knockouts_heading ?? 'Knockout results to submit' }}
                            </h3>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-red-800/90 dark:text-red-300">
                                Outstanding knockout results assigned to your account.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="ui-card overflow-hidden border-red-200/80 bg-transparent ring-1 ring-red-200/80 dark:border-red-900/60 dark:ring-red-900/60">
                            <div class="ui-card-rows divide-red-200/80 dark:divide-red-900/60">
                                @foreach ($resultSubmissionPrompt->knockouts as $knockoutPrompt)
                                    <a href="{{ $knockoutPrompt['url'] }}"
                                        class="block">
                                        <div
                                            class="ui-card-row items-start bg-linear-to-r from-red-50 via-red-50/95 to-amber-50/70 px-4 py-3 text-red-900 transition hover:bg-linear-to-r hover:from-red-100 hover:via-red-50 hover:to-amber-100 dark:from-red-950/40 dark:via-red-950/30 dark:to-amber-950/10 dark:text-red-200 dark:hover:from-red-950/55 dark:hover:via-red-950/40 dark:hover:to-amber-950/20">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-semibold text-red-900 dark:text-red-100">
                                                    {{ $knockoutPrompt['label'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
