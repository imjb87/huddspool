@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-knockout-submit-page>
        <div class="ui-section" data-section-shared-header data-knockout-submit-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Knockouts', 'url' => route('knockout.index')],
                    ['label' => $match->round?->knockout?->name ?? 'Unassigned knockout', 'url' => $match->round?->knockout ? route('knockout.show', $match->round->knockout) : null],
                    ['label' => 'Submit result', 'current' => true],
                ]" />
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="min-w-0">
                        <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">Submit a result</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-5 text-gray-500 dark:text-gray-400">
                            Enter the final score for {{ $match->round?->knockout?->name ?? 'this knockout' }}.
                        </p>
                    </div>

                    <a href="{{ route('knockout.show', $match->round->knockout) }}" class="ui-result-button ui-result-button-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left size-4" aria-hidden="true">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg>
                        <span>Back to knockout</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <section class="ui-section" data-knockout-submit-context>
                    <div class="ui-shell-grid">
                        <div class="ui-section-intro gap-2">
                            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tournament size-5 text-neutral-700 dark:text-neutral-200">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M2 4a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M18 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M2 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M2 20a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M6 12h3a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-3" />
                                    <path d="M6 4h7a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-2" />
                                    <path d="M14 10h4" />
                                </svg>
                            </span>
                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Match details</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                                    Review the matchup before entering the result.
                                </p>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="ui-card" data-knockout-submit-match-details>
                                <div class="ui-card-body">
                                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Match</p>
                                            <p class="flex flex-wrap items-center gap-x-1 text-sm font-semibold text-neutral-950 dark:text-neutral-50">
                                                {{ $match->homeParticipant?->display_name ?? 'TBC' }}
                                                <span class="font-normal text-neutral-400 dark:text-neutral-500">vs</span>
                                                {{ $match->awayParticipant?->display_name ?? 'TBC' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Knockout</p>
                                            <p class="text-sm text-neutral-950 dark:text-neutral-50">{{ $match->round?->knockout?->name ?? 'Unassigned knockout' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Round</p>
                                            <p class="text-sm text-neutral-950 dark:text-neutral-50">{{ $match->round?->name ?? 'Unscheduled round' }}</p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Format</p>
                                            <p class="text-sm text-neutral-950 dark:text-neutral-50">Best of {{ $match->bestOfValue() }} frames</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ui-section" data-knockout-submit-shell>
                    <div class="ui-shell-grid">
                        <div class="ui-section-intro gap-2">
                            <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-numbers size-5 text-neutral-700 dark:text-neutral-200">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M11 6h9" />
                                    <path d="M11 12h9" />
                                    <path d="M12 18h8" />
                                    <path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" />
                                    <path d="M6 10v-6l-2 2" />
                                </svg>
                            </span>
                            <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                                <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Match score</h2>
                                <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                                    Enter the final score. First to {{ $match->targetScoreToWin() }} wins.
                                </p>
                            </div>
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
