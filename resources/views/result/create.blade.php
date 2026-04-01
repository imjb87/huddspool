@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    <div class="ui-page-shell" data-result-create-page>
        <div class="ui-section" data-section-shared-header>
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
                                <p class="text-xs text-gray-500 dark:text-gray-400">Submit a result</p>
                                <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $fixture->section->name }}</h1>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                <section class="ui-section" data-result-create-info-section>
                    <div class="ui-shell-grid">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fixture details</h3>
                            <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Review the fixture details before entering the result.
                            </p>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="ui-card">
                                <div class="ui-card-body">
                                    <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Match</p>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $fixture->homeTeam->name }} <span class="font-normal text-gray-400 dark:text-neutral-500">vs</span>
                                            {{ $fixture->awayTeam->name }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Date</p>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $fixture->fixture_date->format('l jS F Y') }}</p>
                                        </div>

                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Venue</p>
                                            @if ($fixture->venue)
                                                <a class="ui-link inline-flex text-sm font-semibold"
                                                    href="{{ route('venue.show', $fixture->venue->id) }}">
                                                    {{ $fixture->venue->name }}
                                                </a>
                                            @else
                                                <p class="text-sm text-gray-900 dark:text-gray-100">Venue TBC</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <livewire:result-form :fixture="$fixture" />
            </div>
        </div>
    </div>
@endsection
