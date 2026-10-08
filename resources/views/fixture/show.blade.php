@extends('layouts.app')

@section('uses-livewire', 'true')

@section('content')
    @php
        $section = $fixture->section;
        $ruleset = $section?->ruleset;
    @endphp
    <div class="ui-page-shell" data-fixture-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="[
                    ['label' => 'Rulesets'],
                    ['label' => $ruleset?->name ?? 'Ruleset', 'url' => $ruleset ? route('ruleset.show', $ruleset) : null],
                    ['label' => $section?->name ?? 'Section', 'url' => $section && $ruleset ? route('ruleset.section.show', ['ruleset' => $ruleset, 'section' => $section]) : null],
                    ['label' => 'Fixture', 'current' => true],
                ]" />
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $fixture->section->name }}</h1>
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
