<section class="ui-section" data-home-news>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="ui-shell-grid">
            <div class="ui-section-intro gap-2">
                <span class="flex size-6 shrink-0 items-center justify-center" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-news size-5 text-neutral-700 dark:text-neutral-200">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" />
                        <path d="M8 8l4 0" />
                        <path d="M8 12l4 0" />
                        <path d="M8 16l4 0" />
                    </svg>
                </span>
                <div class="ui-section-intro-copy grid auto-rows-min items-start gap-1.5">
                    <h2 class="font-heading text-base leading-6 font-medium text-neutral-900 dark:text-neutral-50">Latest news</h2>
                    <p class="m-0 text-sm leading-5 text-neutral-500 dark:text-neutral-400">
                        Read league announcements, fixture changes, and key dates.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-2">
                @if ($news->isEmpty())
                    <div class="ui-card" data-home-news-empty>
                        <x-ui-empty-state
                            title="No league news has been published yet."
                            description="League announcements will appear here when they are published."
                        />
                    </div>
                @else
                    <div class="flex flex-col gap-4" data-home-news-rows data-home-news-list>
                        @foreach ($news as $article)
                            <article class="ui-card ui-home-news-card" data-home-news-item data-home-news-card>
                                <div class="ui-card-body">
                                    <div class="space-y-4">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $article->published_at?->toDateString() ?? $article->created_at?->toDateString() }}">
                                                {{ $article->published_at?->format('j F Y') ?? $article->created_at?->format('j F Y') }}
                                            </time>
                                        </div>

                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $article->title }}
                                        </h3>

                                        <p class="line-clamp-4 text-sm leading-6 text-gray-600 dark:text-gray-400">
                                            {{ $article->excerpt(220) }}
                                        </p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
