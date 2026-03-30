<div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
    <section class="rounded-xl border border-red-200 bg-red-50 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200"
        data-account-result-submission-prompt>
        <div class="ui-card-body space-y-5">
            @if (count($resultSubmissionPrompt->fixtures ?? []) > 0 || $resultSubmissionPrompt->url)
                <div class="ui-shell-grid">
                    <div>
                        <h3 class="text-sm font-semibold text-red-900 dark:text-red-100">
                            {{ $resultSubmissionPrompt->fixtures_heading ?? 'League results to submit' }}
                        </h3>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-red-800 dark:text-red-300">
                            Outstanding league results assigned to your account.
                        </p>
                        <p class="mt-1 text-sm font-medium text-red-900 dark:text-red-100">
                            {{ $resultSubmissionPrompt->message }}
                        </p>
                        @if ($resultSubmissionPrompt->fixture_label)
                            <p class="mt-1 text-xs text-red-800 dark:text-red-300">{{ $resultSubmissionPrompt->fixture_label }}</p>
                        @endif
                    </div>

                    <div class="lg:col-span-2">
                        @if ($resultSubmissionPrompt->url)
                            <div class="flex justify-start lg:justify-end">
                                <a href="{{ $resultSubmissionPrompt->url }}"
                                    class="inline-flex h-8 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                                    {{ $resultSubmissionPrompt->button_label ?? 'Submit result' }}
                                </a>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($resultSubmissionPrompt->fixtures as $fixturePrompt)
                                    <a href="{{ $fixturePrompt['url'] }}"
                                        class="block text-sm font-medium text-red-800 underline decoration-red-300 underline-offset-3 transition hover:text-red-900 hover:decoration-red-500 dark:text-red-200 dark:decoration-red-800 dark:hover:text-red-100 dark:hover:decoration-red-600">
                                        {{ $fixturePrompt['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if (count($resultSubmissionPrompt->knockouts ?? []) > 0)
                <div class="ui-shell-grid">
                    <div>
                        <h3 class="text-sm font-semibold text-red-900 dark:text-red-100">
                            {{ $resultSubmissionPrompt->knockouts_heading ?? 'Knockout results to submit' }}
                        </h3>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-red-800 dark:text-red-300">
                            Outstanding knockout results assigned to your account.
                        </p>
                    </div>

                    <div class="space-y-2 lg:col-span-2">
                        @foreach ($resultSubmissionPrompt->knockouts as $knockoutPrompt)
                            <a href="{{ $knockoutPrompt['url'] }}"
                                class="block text-sm font-medium text-red-800 underline decoration-red-300 underline-offset-3 transition hover:text-red-900 hover:decoration-red-500 dark:text-red-200 dark:decoration-red-800 dark:hover:text-red-100 dark:hover:decoration-red-600">
                                {{ $knockoutPrompt['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
