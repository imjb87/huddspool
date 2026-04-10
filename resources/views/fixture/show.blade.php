@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    @php
        $section = $fixture->section;
        $ruleset = $section?->ruleset;
    @endphp
    <div class="ui-page-shell" data-fixture-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Rulesets'],
                    ['label' => $ruleset?->name ?? 'Ruleset', 'url' => $ruleset ? route('ruleset.show', $ruleset) : null],
                    ['label' => $section?->name ?? 'Section', 'url' => $section && $ruleset ? route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) : null],
                    ['label' => 'Fixture', 'current' => true],
                ]" />
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0">
                            <div class="ui-page-title-with-icon">
                                <div class="ui-page-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75ZM3 10.5h18" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $fixture->section->name }}</h1>
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
                @include('fixture.partials.submission-prompt')
                @include('fixture.partials.info-section')
                @include('fixture.partials.head-to-head-section')
                <livewire:fixture.team-section
                    :team="$fixture->homeTeam"
                    :section="$fixture->section"
                    :title="$fixture->homeTeam->name"
                    section-key="fixture-home-team-section"
                    side="home" />
                <livewire:fixture.team-section
                    :team="$fixture->awayTeam"
                    :section="$fixture->section"
                    :title="$fixture->awayTeam->name"
                    section-key="fixture-away-team-section"
                    side="away" />
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
