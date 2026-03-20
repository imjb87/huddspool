<section class="bg-white py-10 dark:bg-zinc-900 sm:py-12" data-home-news>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Latest news</h2>
                <p class="max-w-sm text-sm leading-6 text-gray-600 dark:text-gray-400">
                    Important updates, date changes and notices from across the league in one place.
                </p>
            </div>

            <div class="lg:col-span-2">
                @if (! $featuredArticle)
                    <div class="border-t border-gray-200 pt-6 dark:border-zinc-800" data-home-news-empty>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No league news has been published yet.</p>
                        <p class="mt-2 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                            Updates from the league committee will appear here when they are posted.
                        </p>
                    </div>
                @else
                    <div class="border-t border-gray-200 pt-6 dark:border-zinc-800" data-home-news-grid>
                        <article data-home-news-featured>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $featuredArticle->created_at?->toDateString() }}">
                                    {{ $featuredArticle->created_at?->format('j F Y') }}
                                </time>
                            </div>

                            <h3 class="mt-3 text-lg font-semibold tracking-tight text-gray-900 dark:text-gray-100 sm:text-xl">
                                {{ $featuredArticle->title }}
                            </h3>

                            <div class="mt-4 space-y-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                @foreach ($featuredParagraphs as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>

                        @if ($secondaryArticles->isNotEmpty())
                            <div class="mt-6 divide-y divide-gray-200 border-t border-gray-200 dark:divide-zinc-800 dark:border-zinc-800" data-home-news-list>
                                @foreach ($secondaryArticles as $article)
                                    <article class="py-5 first:pt-4 last:pb-0" data-home-news-item>
                                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $article->created_at?->toDateString() }}">
                                                {{ $article->created_at?->format('j F Y') }}
                                            </time>
                                        </div>

                                        <h3 class="mt-3 text-base font-semibold tracking-tight text-gray-900 dark:text-gray-100">
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
                @endif
            </div>
        </div>
    </div>
</section>
