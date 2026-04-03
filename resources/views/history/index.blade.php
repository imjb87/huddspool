@extends('layouts.app')

@section('content')
    <div class="ui-page-shell">
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'History', 'current' => true],
                ]" />
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0">
                            <div class="ui-page-title-with-icon">
                                <div class="ui-page-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2.25m6-2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">History</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <section class="ui-section">
                    <div class="ui-shell-grid">
                        <div class="ui-section-intro">
                            <div class="ui-section-intro-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.11 1.5 2.064v8.4a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18.975v-8.4c0-.954.616-1.78 1.5-2.064m15.75 0A48.108 48.108 0 0 0 12 7.5a48.108 48.108 0 0 0-8.25 1.011m16.5 0v-.891A2.25 2.25 0 0 0 18 5.37l-1.5-.3A2.25 2.25 0 0 1 14.7 3.6l-.15-.75A2.25 2.25 0 0 0 12.3 1.5h-.6a2.25 2.25 0 0 0-2.25 1.35l-.15.75A2.25 2.25 0 0 1 7.5 5.07l-1.5.3a2.25 2.25 0 0 0-2.25 2.25v.891" />
                                </svg>
                            </div>
                            <div class="ui-section-intro-copy">
                                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Season archive</h2>
                                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Browse past seasons, then drill into each ruleset and section for archived standings, fixtures, results, and averages.
                                </p>
                            </div>
                        </div>

                        <div class="lg:col-span-2" data-history-index-accordion
                            x-data="{ openSeason: null, openRuleset: null }">
                            @forelse ($seasonGroups as $group)
                                @if ($loop->first)
                                    <div class="ui-card">
                                        <div class="ui-card-rows">
                                @endif
                                @include('history.partials.season-group')
                                @if ($loop->last)
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="ui-card">
                                    <div class="ui-card-body py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No historical seasons are available yet.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
