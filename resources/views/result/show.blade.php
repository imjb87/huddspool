@extends('layouts.app')

@section('content')
    @php
        $fixture = $result->fixture;
        $section = $fixture->section;
        $season = $fixture->season;
        $ruleset = $section?->ruleset;
        $sectionLink = null;

        if ($section && $ruleset) {
            $sectionLink = $season && $season->hasConcluded()
                ? route('history.section.show', [
                    'season' => $season,
                    'ruleset' => $ruleset,
                    'section' => $section,
                    'tab' => 'fixtures-results',
                ])
                : route('ruleset.section.show', [
                    'ruleset' => $ruleset,
                    'section' => $section,
                    'tab' => 'fixtures-results',
                ]);
        }
    @endphp
    <div class="bg-gray-50 pt-[72px] dark:bg-zinc-900">
        <div class="pb-10 lg:pb-14" data-result-page>
            <div class="mx-auto flex w-full max-w-4xl items-end justify-between gap-3 px-4 pt-6 pb-4 sm:px-6 lg:px-6 lg:pt-7 lg:pb-4"
                data-section-shared-header>
                <div class="min-w-0">
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Result</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $section?->name ?? 'Archived section' }}</p>
                </div>
            </div>

            <div class="mx-auto max-w-4xl px-4 pt-2 sm:px-6 lg:px-6">
                <div class="space-y-6">
                    @include('result.partials.info-section')
                    @include('result.partials.card-section')
                </div>
            </div>
        </div>

        <x-logo-clouds  />
    </div>
@endsection
