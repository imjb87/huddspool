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
            $breadcrumbItems[] = ['label' => 'History', 'url' => route('history.index')];
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
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb class="mb-3" :items="$breadcrumbItems" />
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="min-w-0">
                            <div class="ui-page-title-with-icon">
                                <div class="ui-page-title-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M9 5.25H7.5A2.25 2.25 0 0 0 5.25 7.5v9A2.25 2.25 0 0 0 7.5 18.75h9A2.25 2.25 0 0 0 18.75 16.5v-9A2.25 2.25 0 0 0 16.5 5.25H15m-6 0V3.75A.75.75 0 0 1 9.75 3h4.5a.75.75 0 0 1 .75.75v1.5m-6 0h6" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h1 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $section?->name ?? 'Archived section' }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end"
                        x-data="{
                            shareUrl: @js($shareUrl),
                            shareTitle: @js($shareTitle),
                            async shareCard() {
                                if (navigator.share) {
                                    try {
                                        await navigator.share({
                                            title: this.shareTitle,
                                            text: 'View the shared result card',
                                            url: this.shareUrl,
                                        });
                                        return;
                                    } catch (error) {
                                        if (error?.name === 'AbortError') {
                                            return;
                                        }
                                    }
                                }

                                if (navigator.clipboard?.writeText) {
                                    await navigator.clipboard.writeText(this.shareUrl);
                                    return;
                                }
                            },
                        }">
                        <button type="button"
                            class="ui-button-secondary gap-2"
                            x-on:click="shareCard()"
                            data-result-share-card-button>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                            </svg>
                            <span>Share result</span>
                        </button>
                    </div>
                </div>
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
