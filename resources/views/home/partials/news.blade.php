<section class="ui-section" data-home-news>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Latest news</h2>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-600 dark:text-gray-400">
                    Important updates, date changes and notices from across the league in one place.
                </p>
            </div>

            <div class="lg:col-span-2">
                @if (! $featuredArticle)
                    <div class="ui-card" data-home-news-empty>
                        <div class="ui-card-body">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No league news has been published yet.</p>
                            <p class="mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                Updates from the league committee will appear here when they are posted.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="ui-card" data-home-news-grid>
                        <div class="ui-card-body">
                            <article data-home-news-featured>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <time datetime="{{ $featuredArticle->created_at?->toDateString() }}">
                                        {{ $featuredArticle->created_at?->format('j F Y') }}
                                    </time>
                                </div>

                                <h3 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $featuredArticle->title }}
                                </h3>

                                <div class="mt-4 space-y-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                    @foreach ($featuredParagraphs as $paragraph)
                                        <p>{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </article>

                            @if ($secondaryArticles->isNotEmpty())
                                <div class="ui-card-rows mt-6 border-t border-gray-200 dark:border-zinc-700/75" data-home-news-list>
                                    @foreach ($secondaryArticles as $article)
                                        <article class="px-0 py-5 first:pt-4 last:pb-0" data-home-news-item>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                <time datetime="{{ $article->created_at?->toDateString() }}">
                                                    {{ $article->created_at?->format('j F Y') }}
                                                </time>
                                            </div>

                                            <h3 class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $article->title }}
                                            </h3>

                                            <p class="mt-3 line-clamp-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                                {{ \Illuminate\Support\Str::limit($article->content, 180) }}
                                            </p>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
