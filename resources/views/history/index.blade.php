@extends('layouts.app')

@section('content')
    <div class="ui-page-shell">
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">History</p>
                        <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">History</h1>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <section class="ui-section">
                    <div class="ui-shell-grid">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Season archive</h2>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Browse past seasons, then drill into each ruleset and section for archived standings, fixtures, results, and averages.
                            </p>
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
