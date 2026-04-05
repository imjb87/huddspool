<section class="ui-section" data-home-news>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div class="ui-section-intro">
                <div class="ui-section-intro-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.5v9a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 16.5v-9m15 0A2.25 2.25 0 0 0 17.25 5.25H6.75A2.25 2.25 0 0 0 4.5 7.5m15 0v.75A2.25 2.25 0 0 1 17.25 10.5H6.75A2.25 2.25 0 0 1 4.5 8.25V7.5m4.5 6h6" />
                    </svg>
                </div>
                <div class="ui-section-intro-copy">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Latest news</h2>
                    <p class="mt-1 max-w-sm text-sm leading-6 text-gray-600 dark:text-gray-400">
                        Important updates, date changes and notices from across the league in one place.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                @if ($news->isEmpty())
                    <div class="ui-card" data-home-news-empty>
                        <div class="ui-card-body">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No league news has been published yet.</p>
                            <p class="mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                Updates from the league committee will appear here when they are posted.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="ui-card" data-home-news-rows>
                        <div class="ui-card-body">
                            <div class="ui-card-rows" data-home-news-list>
                                @foreach ($news as $article)
                                    <article class="px-0 py-5 first:pt-0 last:pb-0" data-home-news-item>
                                        <a href="{{ route('news.show', $article) }}" class="block space-y-4 rounded-2xl transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-green-400 dark:focus-visible:ring-offset-gray-900">
                                            @if ($article->featured_image_url)
                                                <div class="overflow-hidden rounded-2xl" data-home-news-featured-image>
                                                    <img
                                                        src="{{ $article->featured_image_url }}"
                                                        alt="{{ $article->title }} featured image"
                                                        class="h-40 w-full object-cover"
                                                    >
                                                </div>
                                            @endif

                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                <time datetime="{{ $article->published_at?->toDateString() ?? $article->created_at?->toDateString() }}">
                                                    {{ $article->published_at?->format('j F Y') ?? $article->created_at?->format('j F Y') }}
                                                </time>
                                            </div>

                                            <h3 class="text-base font-semibold text-gray-900 transition hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300">
                                                {{ $article->title }}
                                            </h3>

                                            <p class="line-clamp-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                                {{ $article->excerpt(220) }}
                                            </p>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($news->isNotEmpty())
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('news.index') }}" class="ui-button-secondary">
                            View all news
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
