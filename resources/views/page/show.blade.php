@extends('layouts.app')

@section('content')
    <div class="ui-page-shell" data-page-show>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="ui-page-title-with-icon">
                            <div class="ui-page-title-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m2.25 6h4.875A2.625 2.625 0 0 1 18 10.875v8.625A2.625 2.625 0 0 1 15.375 22.125h-6.75A2.625 2.625 0 0 1 6 19.5V4.5A2.25 2.25 0 0 1 8.25 2.25h1.5A2.25 2.25 0 0 1 12 4.5v1.5a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Page</p>
                                <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $page->title }}</h1>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <section class="ui-section" data-page-content-section>
                <div class="prose prose-gray max-w-none text-sm leading-7 text-gray-700 dark:prose-invert dark:text-gray-300" data-page-content>
                    {!! $page->content !!}
                </div>
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
