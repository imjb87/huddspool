@extends('layouts.app')

@section('content')
    @php
        $hasRulesetContent = filled(trim(strip_tags((string) $ruleset->content)));
    @endphp

    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-ruleset-content-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ruleset->name }}</h1>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-6 sm:px-6 lg:px-6">
                @if ($hasRulesetContent)
                    <section class="py-1" data-ruleset-content-section>
                        <div class="prose prose-gray dark:prose-invert max-w-none text-sm leading-7 text-gray-700 dark:text-gray-300">
                            {!! $ruleset->content !!}
                        </div>
                    </section>
                @else
                    <section class="py-1" data-ruleset-content-empty>
                        <div class="prose prose-gray dark:prose-invert max-w-none text-sm leading-7 text-gray-700 dark:text-gray-300">
                            <p>No ruleset content has been published yet.</p>
                        </div>
                    </section>
                @endif
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
