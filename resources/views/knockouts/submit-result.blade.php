@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 pt-[72px] pb-10 lg:pb-14 dark:bg-zinc-950" data-knockout-submit-page>
        <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
            data-section-shared-header
            data-knockout-submit-header>
            <div class="min-w-0">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Submit knockout result</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}
                    <span class="text-gray-300 dark:text-zinc-600">/</span>
                    {{ $match->round?->name ?? 'Unscheduled round' }}
                </p>
            </div>
        </div>

        <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <section class="py-1" data-knockout-submit-context>
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match details</h3>
                            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Record the final score for this knockout tie. Existing access rules and validation still apply.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</p>
                                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $match->homeParticipant?->display_name ?? 'TBC' }}
                                        <span class="font-normal text-gray-400 dark:text-gray-500">vs</span>
                                        {{ $match->awayParticipant?->display_name ?? 'TBC' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Knockout</p>
                                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Round</p>
                                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">{{ $match->round?->name ?? 'Unscheduled round' }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Format</p>
                                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">Best of {{ $match->bestOfValue() }} frames</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-knockout-submit-shell>
                    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
                        <div class="space-y-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match score</h3>
                            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Enter the final score. First to {{ $match->targetScoreToWin() }} wins.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <livewire:knockout.submit-result :match="$match" />
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
