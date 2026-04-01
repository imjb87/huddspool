@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-knockout-submit-page>
        <div class="ui-section" data-section-shared-header data-knockout-submit-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="ui-page-title-with-icon">
                            <div class="ui-page-title-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M9 5.25H7.5A2.25 2.25 0 0 0 5.25 7.5v9A2.25 2.25 0 0 0 7.5 18.75h9A2.25 2.25 0 0 0 18.75 16.5v-9A2.25 2.25 0 0 0 16.5 5.25H15m-6 0V3.75A.75.75 0 0 1 9.75 3h4.5a.75.75 0 0 1 .75.75v1.5m-6 0h6" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Submit knockout result</p>
                                <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}</h1>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <section class="ui-section" data-knockout-submit-context>
                    <div class="ui-shell-grid">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match details</h3>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Record the final score for this knockout tie. Existing access rules and validation still apply.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="ui-card">
                                <div class="ui-card-body">
                                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Match</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $match->homeParticipant?->display_name ?? 'TBC' }}
                                                <span class="font-normal text-gray-400 dark:text-gray-500">vs</span>
                                                {{ $match->awayParticipant?->display_name ?? 'TBC' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Knockout</p>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Round</p>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $match->round?->name ?? 'Unscheduled round' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Format</p>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">Best of {{ $match->bestOfValue() }} frames</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ui-section" data-knockout-submit-shell>
                    <div class="ui-shell-grid">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Match score</h3>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
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
