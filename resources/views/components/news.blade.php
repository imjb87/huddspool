<div class="bg-gray-100 dark:bg-gray-800 rounded-lg duration-500 p-4 md:p-6 shadow">
    <h2 class="dark:text-white text-[10px] font-semibold duration-500 uppercase tracking-widest">Latest News</h2>
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach( $news as $article )
            <li class="dark:text-white duration-500 py-4 md:py-6">
                <h3 class="font-semibold text-lg">{{ $article->title }}</h3>
                <span class="text-gray-500 text-[10px] uppercase tracking-widest">{{ $article->created_at->diffForHumans() }}</span>
                <p class="mt-2 text-sm max-w-prose">{!! nl2br(e($article->content)) !!}</p>
            </li>
        @endforeach
    </ul>
</div>