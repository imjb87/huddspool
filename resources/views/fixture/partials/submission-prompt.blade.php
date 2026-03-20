@if ($canSubmitResult)
    <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-900/60 dark:bg-red-950/40">
            <div class="min-w-0">
                <p class="text-sm font-medium text-red-700 dark:text-red-300">Result submission</p>
                <p class="text-sm text-red-900 dark:text-red-100">{{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}</p>
            </div>

            @if ($submissionIsOpen)
                <a href="{{ route('result.create', $fixture) }}"
                    class="inline-flex items-center rounded-full bg-linear-to-br from-red-700 via-red-600 to-red-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-red-800 hover:via-red-700 hover:to-red-600">
                    Submit result
                </a>
            @else
                <span
                    class="inline-flex items-center rounded-full bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 dark:bg-zinc-700 dark:text-gray-300">
                    Opens {{ $fixture->fixture_date->format('j M Y') }}
                </span>
            @endif
        </div>
    </div>
@endif
