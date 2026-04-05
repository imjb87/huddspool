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
@if ($newsArticle->featured_image_url)
    @section('og_image', $newsArticle->featured_image_url)
@endif

@section('content')
    <div class="ui-page-shell bg-neutral-100 dark:bg-neutral-950" data-news-show>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <x-ui-breadcrumb :items="[
                    ['label' => 'News', 'url' => route('news.index')],
                    ['label' => $newsArticle->title, 'current' => true],
                ]" />
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <section class="ui-section">
                <div class="ui-card">
                    @if ($newsArticle->featured_image_url)
                        <div class="border-b border-gray-200 dark:border-gray-800" data-news-featured-image>
                            <img
                                src="{{ $newsArticle->featured_image_url }}"
                                alt="{{ $newsArticle->title }} featured image"
                                class="h-56 w-full object-cover sm:h-72"
                            >
                        </div>
                    @endif
                    <div class="ui-card-body">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <time datetime="{{ $newsArticle->created_at?->toDateString() }}">
                                {{ $newsArticle->created_at?->format('j F Y') }}
                            </time>
                        </div>

                        <h1 class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $newsArticle->title }}
                        </h1>

                        <div class="mt-4 space-y-4 text-sm leading-7 text-gray-700 dark:text-gray-300" data-news-content>
                            @foreach (preg_split('/\r\n|\r|\n/', trim((string) $newsArticle->content)) as $paragraph)
                                @if (filled(trim((string) $paragraph)))
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="mt-4 flex justify-end"
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                        </svg>
                        <span>Share article</span>
                    </button>
                </div>
            </section>

        </div>

        <x-logo-clouds />
    </div>
@endsection
