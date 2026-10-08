@extends('layouts.app')

@section('title', 'Result')

@section('content')
    @php
        $fixture = $result->fixture;
        $section = $fixture->section;
        $season = $fixture->season;
        $ruleset = $section?->ruleset;
        $sectionLink = null;
        $breadcrumbItems = [];

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

        if ($season && $season->hasConcluded()) {
            $breadcrumbItems[] = ['label' => 'History'];
            $breadcrumbItems[] = ['label' => $season->name];
        } else {
            $breadcrumbItems[] = ['label' => 'Rulesets'];
            $breadcrumbItems[] = ['label' => $ruleset?->name ?? 'Ruleset', 'url' => $ruleset ? route('ruleset.show', $ruleset) : null];
        }

        $breadcrumbItems[] = ['label' => $section?->name ?? 'Archived section', 'url' => $sectionLink];
        $breadcrumbItems[] = ['label' => 'Result', 'current' => true];

        $publicOrigin = request()->getSchemeAndHttpHost();
        $shareUrl = $publicOrigin.route('result.show', $result, false);
        $shareImageUrl = $publicOrigin.route('result.og-image', $result, false);
        $shareTitle = $result->home_team_name.' '.$result->home_score.'-'.$result->away_score.' '.$result->away_team_name;
        $shareDescription = collect([
            $section?->name,
            $ruleset?->name,
            $fixture->fixture_date?->format('j M Y'),
        ])->filter()->implode(' / ');
    @endphp
    @section('meta_description', $shareDescription ?: config('app.description'))
    @section('og_title', $shareTitle)
    @section('og_description', $shareDescription ?: config('app.description'))
    @section('og_type', 'article')
    @section('og_url', $shareUrl)
    @section('og_image', $shareImageUrl)
    @section('og_image_type', 'image/png')
    @section('og_image_width', '1200')
    @section('og_image_height', '630')
    <div class="ui-page-shell" data-result-page>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="$breadcrumbItems" />
                <h1 class="min-w-0 text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $section?->name ?? 'Archived section' }}</h1>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <div class="space-y-6">
                @include('result.partials.info-section')
                @include('result.partials.card-section')
            </div>
        </div>

        <x-logo-clouds />
    </div>
@endsection
