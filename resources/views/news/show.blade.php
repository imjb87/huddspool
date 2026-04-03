@extends('layouts.app')

@section('title', $newsArticle->title)

@php
    $shareUrl = request()->getSchemeAndHttpHost().route('news.show', $newsArticle, false);
    $shareTitle = $newsArticle->title;
    $shareDescription = $newsArticle->excerpt(180);
@endphp

@section('meta_description', $shareDescription ?: config('app.description'))
@section('og_title', $shareTitle)
@section('og_description', $shareDescription ?: config('app.description'))
@section('og_type', 'article')
@section('og_url', $shareUrl)

@section('content')
    <div class="ui-page-shell bg-neutral-100 dark:bg-neutral-950" data-news-show>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="space-y-4">
                    <div class="flex justify-end"
                        x-data="{
                            shareUrl: @js($shareUrl),
                            shareTitle: @js($shareTitle),
                            async shareArticle() {
                                if (navigator.share) {
                                    try {
                                        await navigator.share({
                                            title: this.shareTitle,
                                            text: 'Read this Huddspool news update',
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
                                }
                            },
                        }">
                        <button type="button"
                            class="ui-button-secondary gap-2"
                            x-on:click="shareArticle()"
                            data-news-share-button>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V3.75m0 12.75 4.5-4.5m-4.5 4.5-4.5-4.5M3.75 15v3A2.25 2.25 0 0 0 6 20.25h12A2.25 2.25 0 0 0 20.25 18v-3" />
                            </svg>
                            <span>Share article</span>
                        </button>
                    </div>

                    <div class="min-w-0">
                        <div class="ui-page-title-with-icon">
                            <div class="ui-page-title-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.5v9a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5v-9m15 0A2.25 2.25 0 0 0 17.25 5.25H6.75A2.25 2.25 0 0 0 4.5 7.5m15 0v.75A2.25 2.25 0 0 1 17.25 10.5H6.75A2.25 2.25 0 0 1 4.5 8.25V7.5m4.5 6h6" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400">News</p>
                                <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">{{ $newsArticle->title }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <section class="ui-section">
                <div class="ui-card">
                    <div class="ui-card-body">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <time datetime="{{ $newsArticle->created_at?->toDateString() }}">
                                {{ $newsArticle->created_at?->format('j F Y') }}
                            </time>
                        </div>

                        <div class="mt-6 space-y-4 text-sm leading-7 text-gray-700 dark:text-gray-300" data-news-content>
                            @foreach (preg_split('/\r\n|\r|\n/', trim((string) $newsArticle->content)) as $paragraph)
                                @if (filled(trim((string) $paragraph)))
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="ui-section mt-4">
                <div class="flex justify-end">
                    <a href="{{ route('news.index') }}" class="ui-button-secondary">
                        Back to all news
                    </a>
                </div>
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
