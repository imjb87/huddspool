<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">News</h1>
            <p class="mt-2 text-sm text-gray-700">All news articles.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-none">
            <a href="{{ route('admin.news.create') }}"
                class="block rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                news</a>
        </div>
    </div>
    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                @if ($news->count() > 0)
                    <div class="overflow-hidden bg-white shadow sm:rounded-md">
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach ($news as $article)
                                <li>
                                    <a class="block hover:bg-gray-50" href="{{ route('admin.news.show', $article) }}">
                                        <div class="flex items-center px-4 py-4 sm:px-6">
                                            <div class="flex min-w-0 flex-1 items-center">
                                                <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                                    <div>
                                                        <p class="truncate text-sm font-medium text-gray-600">
                                                            {{ $article->created_at->format('d/m/Y') }} -
                                                            {{ $article->title }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                    fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-4">
                        {{ $news->links() }}
                    </div>
                @else
                    <a href="{{ route('admin.news.create') }}"
                        class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="mt-2 block text-sm font-semibold text-gray-900">Add news</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
