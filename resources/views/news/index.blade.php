@extends('layouts.app')

@section('title', 'News')

@section('content')
    <div class="ui-page-shell ui-document-page" data-news-index>
        <x-ui-document-header
            :breadcrumbs="[['label' => 'News', 'current' => true]]"
            title="League updates"
            description="Read league announcements, fixture changes, and key dates."
        />

        <div class="ui-document-body">
            <section class="ui-section" data-news-index-section>
                @if ($news->isEmpty())
                    <div class="ui-card ui-document-card ui-document-empty">
                        <x-ui-empty-state title="No league news has been published yet." />
                    </div>
                @else
                    <div class="ui-document-list" data-news-index-list>
                        @foreach ($news as $article)
                            <article class="ui-card ui-document-card transition-shadow hover:shadow-sm" data-news-index-item>
                                    @if ($article->featured_image_url)
                                        <div class="border-b border-border" data-news-index-featured-image>
                                            <img
                                                src="{{ $article->featured_image_url }}"
                                                alt="{{ $article->title }} featured image"
                                                class="h-48 w-full object-cover sm:h-56"
                                            >
                                        </div>
                                    @endif
                                <div class="ui-document-card-body">
                                    <div class="text-xs text-muted-foreground">
                                        <time datetime="{{ $article->created_at?->toDateString() }}">
                                            {{ $article->created_at?->format('j F Y') }}
                                        </time>
                                    </div>

                                    <h2 class="mt-2 text-xl font-semibold tracking-tight text-foreground">
                                        <a href="{{ route('news.show', $article) }}" class="transition-colors hover:text-muted-foreground">
                                            {{ $article->title }}
                                        </a>
                                    </h2>

                                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                                        {{ $article->excerpt(260) }}
                                    </p>

                                    <div class="mt-6 flex">
                                        <a href="{{ route('news.show', $article) }}" class="ui-link">
                                            See more
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
