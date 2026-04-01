@if ($canSubmitResult)
    <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-900/60 dark:bg-red-950/40">
            <div class="flex min-w-0 items-start gap-3">
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/80 ring-1 ring-red-200/80 shadow-sm dark:bg-red-950/50 dark:ring-red-900/60">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-red-700 dark:text-red-200" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 6.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">Result submission</p>
                    <p class="text-sm text-red-900 dark:text-red-100">{{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}</p>
                </div>
            </div>

            @if ($submissionIsOpen)
                <a href="{{ route('result.create', $fixture) }}"
                    class="inline-flex items-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-red-800 hover:via-red-700 hover:to-red-600">
                    Submit result
                </a>
            @else
                <span
                    class="inline-flex items-center rounded-full bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 dark:bg-neutral-800 dark:text-gray-300">
                    Opens {{ $fixture->fixture_date->format('j M Y') }}
                </span>
            @endif
        </div>
    </div>
@endif
