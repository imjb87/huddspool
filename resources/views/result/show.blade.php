@extends('layouts.app')

@section('title', $shareTitle)

@section('meta')
    <meta property="og:title" content="{{ $shareTitle }}" />
    <meta property="og:description" content="{{ $shareDescription }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('result.show', $result) }}" />
    <meta property="og:image" content="{{ $shareImageUrl }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $shareTitle }}" />
    <meta name="twitter:description" content="{{ $shareDescription }}" />
    <meta name="twitter:image" content="{{ $shareImageUrl }}" />
@endsection

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

                @if ($result->is_confirmed)
                    <button type="button"
                        class="inline-flex shrink-0 items-center justify-center rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-xs transition hover:border-gray-400 hover:text-gray-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-200 dark:hover:border-zinc-500 dark:hover:text-white"
                        data-result-share-button
                        data-share-title="{{ $shareTitle }}"
                        data-share-text="{{ $shareDescription }}"
                        data-share-url="{{ route('result.show', $result) }}">
                        Share result
                    </button>
                @endif
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

@push('scripts')
    @if ($result->is_confirmed)
        <script>
            document.querySelector('[data-result-share-button]')?.addEventListener('click', async (event) => {
                const button = event.currentTarget;
                const url = button.dataset.shareUrl;
                const title = button.dataset.shareTitle;
                const text = button.dataset.shareText;

                try {
                    if (navigator.share) {
                        await navigator.share({
                            title,
                            text,
                            url,
                        });

                        return;
                    }

                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(url);
                        button.textContent = 'Link copied';

                        window.setTimeout(() => {
                            button.textContent = 'Share result';
                        }, 2000);

                        return;
                    }
                } catch (error) {
                    if (error?.name === 'AbortError') {
                        return;
                    }
                }

                window.open(url, '_blank', 'noopener,noreferrer');
            });
        </script>
    @endif
@endpush
