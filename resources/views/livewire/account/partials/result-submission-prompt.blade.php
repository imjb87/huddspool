<div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200" data-account-result-submission-prompt>
        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)] md:items-start md:gap-6">
            <div class="min-w-0">
                <p class="font-medium">{{ $resultSubmissionPrompt->message }}</p>
                @if ($resultSubmissionPrompt->fixture_label)
                    <p class="mt-1 text-xs text-red-800 dark:text-red-300">{{ $resultSubmissionPrompt->fixture_label }}</p>
                @endif
            </div>

            @if ($resultSubmissionPrompt->url)
                <div class="md:flex md:justify-end">
                    <a href="{{ $resultSubmissionPrompt->url }}"
                        class="inline-flex h-8 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 text-xs font-extrabold text-white shadow-sm ring-1 ring-black/10 transition hover:brightness-110">
                        {{ $resultSubmissionPrompt->button_label ?? 'Submit result' }}
                    </a>
                </div>
            @else
                <div class="space-y-3 md:pt-0.5">
                    @if (count($resultSubmissionPrompt->fixtures ?? []) > 0)
                        <div class="space-y-1.5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-red-700/80 dark:text-red-300/80">
                                {{ $resultSubmissionPrompt->fixtures_heading ?? 'League matches' }}
                            </p>

                            @foreach ($resultSubmissionPrompt->fixtures as $fixturePrompt)
                                <a href="{{ $fixturePrompt['url'] }}"
                                    class="block text-sm font-medium text-red-800 underline decoration-red-300 underline-offset-3 transition hover:text-red-900 hover:decoration-red-500 dark:text-red-200 dark:decoration-red-800 dark:hover:text-red-100 dark:hover:decoration-red-600">
                                    {{ $fixturePrompt['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if (count($resultSubmissionPrompt->knockouts ?? []) > 0)
                        <div class="space-y-1.5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-red-700/80 dark:text-red-300/80">
                                {{ $resultSubmissionPrompt->knockouts_heading ?? 'Knockouts' }}
                            </p>

                            @foreach ($resultSubmissionPrompt->knockouts as $knockoutPrompt)
                                <a href="{{ $knockoutPrompt['url'] }}"
                                    class="block text-sm font-medium text-red-800 underline decoration-red-300 underline-offset-3 transition hover:text-red-900 hover:decoration-red-500 dark:text-red-200 dark:decoration-red-800 dark:hover:text-red-100 dark:hover:decoration-red-600">
                                    {{ $knockoutPrompt['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
