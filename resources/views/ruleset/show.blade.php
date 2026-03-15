@extends('layouts.app')

@section('content')
    @php
        $hasRulesetContent = filled(trim(strip_tags((string) $ruleset->content)));
    @endphp

    <div>
        <div class="bg-white pt-24 pb-8 sm:pt-32 sm:pb-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:mx-0">
                    <h1 class="font-serif text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        {{ $ruleset->name }}
                    </h1>
                </div>
            </div>
        </div>

        <div class="pb-8 sm:pb-16" data-ruleset-content-page>
            <div class="mx-auto max-w-7xl px-4 text-base leading-7 text-gray-700 sm:px-6 lg:px-8">
                @if ($hasRulesetContent)
                    <section class="prose mx-auto max-w-none">
                        {!! $ruleset->content !!}
                    </section>
                @else
                    <section class="prose mx-auto max-w-none" data-ruleset-content-empty>
                        <p>No ruleset content has been published yet.</p>
                    </section>
                @endif
            </div>
        </div>

        <x-logo-clouds variant="section-showcase" />
    </div>
@endsection
