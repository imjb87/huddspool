<div class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Latest News</h2>
            <p class="mt-2 text-lg leading-8 text-gray-600">Stay up to date with the latest news from the Huddersfield
                Pool League.</p>
            <div class="mt-10 space-y-16 border-t border-gray-200 pt-10 sm:mt-16 sm:pt-16">
                @foreach ($news as $article)
                    <article class="flex max-w-xl flex-col items-start justify-between">
                        <div class="flex items-center gap-x-4 text-xs">
                            <time class="text-gray-500">{{ $article->created_at->format('l jS F Y') }}</time>
                        </div>
                        <div class="group relative">
                            <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                                {{ $article->title }}
                            </h3>
                            <p class="mt-5 line-clamp-3 text-sm leading-6 text-gray-600">{!! nl2br(e($article->content)) !!}</p>
                        </div>
                    </article>
                @endforeach

                <!-- More posts... -->
            </div>
        </div>
    </div>
</div>
