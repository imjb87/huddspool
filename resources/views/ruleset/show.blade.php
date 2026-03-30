@extends('layouts.app')

@section('content')
    @php
        $hasRulesetContent = filled(trim(strip_tags((string) $ruleset->content)));
    @endphp

    <div class="ui-page-shell" data-ruleset-content-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ruleset</p>
                        <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $ruleset->name }}</h1>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            @if ($hasRulesetContent)
                <section class="ui-section" data-ruleset-content-section>
                    <div class="prose prose-gray max-w-none text-sm leading-7 text-gray-700 dark:prose-invert dark:text-gray-300">
                        {!! $ruleset->content !!}
                    </div>
                </section>
            @else
                <section class="ui-section" data-ruleset-content-empty>
                    <div class="prose prose-gray max-w-none text-sm leading-7 text-gray-700 dark:prose-invert dark:text-gray-300">
                        <p>No ruleset content has been published yet.</p>
                    </div>
                </section>
            @endif
        </div>

        <x-logo-clouds />
    </div>
@endsection
