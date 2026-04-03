@extends('layouts.app')

@section('title', 'News')

@section('content')
    <div class="ui-page-shell bg-neutral-100 dark:bg-neutral-950" data-news-index>
        <div class="ui-section" data-section-shared-header>
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
                <div class="ui-shell-grid grid-cols-[minmax(0,1fr)_auto] items-center lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="ui-page-title-with-icon">
                            <div class="ui-page-title-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-page-title-glyph" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.5v9a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5v-9m15 0A2.25 2.25 0 0 0 17.25 5.25H6.75A2.25 2.25 0 0 0 4.5 7.5m15 0v.75A2.25 2.25 0 0 1 17.25 10.5H6.75A2.25 2.25 0 0 1 4.5 8.25V7.5m4.5 6h6" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400">News</p>
                                <h1 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">League updates</h1>
                            </div>
                        </div>
                    </div>

                    <div aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-6">
            <section class="ui-section">
                @if ($news->isEmpty())
                    <div class="ui-card">
                        <div class="ui-card-body">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No league news has been published yet.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-4" data-news-index-list>
                        @foreach ($news as $article)
                            <article class="ui-card" data-news-index-item>
                                <div class="ui-card-body">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <time datetime="{{ $article->created_at?->toDateString() }}">
                                            {{ $article->created_at?->format('j F Y') }}
                                        </time>
                                    </div>

                                    <h2 class="mt-2 text-base font-semibold text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('news.show', $article) }}" class="transition hover:text-gray-600 dark:hover:text-gray-300">
                                            {{ $article->title }}
                                        </a>
                                    </h2>

                                    <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                        {{ $article->excerpt(260) }}
                                    </p>

                                    <div class="mt-4 flex justify-end">
                                        <a href="{{ route('news.show', $article) }}" class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                            See more...
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <x-logo-clouds />
    </div>
@endsection
