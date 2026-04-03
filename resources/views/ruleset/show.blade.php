@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-ruleset-hub>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0 space-y-3">
                            <x-ui-breadcrumb :items="[
                                ['label' => 'Rulesets'],
                                ['label' => $ruleset->name, 'current' => true],
                            ]" />
                            <div class="ui-page-title-with-icon">
                                <div class="ui-page-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h16.5M3.75 9.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $ruleset->name }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <section class="ui-section" data-ruleset-sections>
                <div class="ui-shell-grid">
                    <div class="ui-section-intro">
                        <div class="ui-section-intro-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5M3 18.75A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75M3 18.75h18M8.25 10.5h7.5" />
                            </svg>
                        </div>
                        <div class="ui-section-intro-copy">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Current sections</h2>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                Choose a section to view current standings, fixtures, results, and averages for {{ $ruleset->name }}.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        @if ($sections->isEmpty())
                            <div class="ui-card" data-ruleset-sections-empty>
                                <div class="ui-card-body py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No open sections are available for this ruleset yet.
                                </div>
                            </div>
                        @else
                            <div class="ui-card" data-ruleset-sections-list>
                                <div class="ui-card-rows">
                                    @foreach ($sections as $section)
                                        <a href="{{ route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) }}"
                                            class="ui-card-row-link">
                                            <div class="ui-card-row items-center justify-between gap-4 px-4 sm:px-5">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $section->name }}</p>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $section->season->name }}</p>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 shrink-0 text-gray-400 dark:text-gray-500" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
