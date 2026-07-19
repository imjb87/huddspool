@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-downloads-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Downloads', 'current' => true],
                ]" />
                <div class="ui-page-title-with-icon">
                    <div class="ui-page-title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5M6.75 3.75h10.5A2.25 2.25 0 0 1 19.5 6v12a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V6a2.25 2.25 0 0 1 2.25-2.25Zm2.25 9 3 3 3-3m-3 3V9" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">Downloads</h1>
                        <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">Printable score cards for league and knockout nights.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="ui-shell-grid">
                <div class="ui-section-intro">
                    <div class="ui-section-intro-copy">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Printable forms</h2>
                        <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">Download a blank PDF, print the copies you need, and complete them on the night.</p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="ui-card overflow-hidden">
                        <div class="ui-card-rows">
                            <a href="{{ asset('downloads/scorecard.pdf') }}" download="scorecard.pdf" class="ui-card-row-link" data-download-link="scorecard">
                                <div class="ui-card-row items-center justify-between gap-4 px-4 sm:px-5">
                                    <div>
                                        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Score card</h2>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Blank match score card for recording frames and the final result.</p>
                                    </div>
                                    <span class="ui-button-secondary shrink-0">Download scorecard.pdf</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
