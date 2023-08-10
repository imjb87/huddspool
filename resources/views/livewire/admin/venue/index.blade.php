<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">Venues</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all the venues.</p>
        </div>
        <!-- Search bar -->
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <div class="relative">
                <input type="text" name="search" id="search" wire:model="search"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Search" />
            </div>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-none">
            <a href="{{ route('admin.venues.create') }}"
                class="block rounded-md bg-indigo-600 py-2 px-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                venue</a>
        </div>
    </div>
    <div class="mt-8 flow-root">
        <div class="-my-2 overflow-hidden">
            <div class="inline-block min-w-full max-w-full py-2 align-middle sm:px-6 lg:px-8">
                @if ($venues->count() > 0)
                    <div class="overflow-hidden bg-white shadow sm:rounded-md">
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach ($venues as $venue)
                                <li>
                                    <a class="block hover:bg-gray-50" href="{{ route('admin.venues.show', $venue) }}">
                                        <div class="flex items-center px-4 py-4 sm:px-6">
                                            <div class="flex min-w-0 flex-1 items-center">
                                                <div class="min-w-0 flex-1 md:grid md:grid-cols-2 md:gap-4">
                                                    <div>
                                                        <p class="truncate text-sm font-medium text-gray-600">
                                                            {{ $venue->name }}</p>
                                                    </div>
                                                    <div class="hidden md:block">
                                                        <div>
                                                            <p class="text-sm text-gray-500 truncate">
                                                                {{ $venue->address }}
                                                            </p>
                                                        </div>
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
                        {{ $venues->links() }}
                    </div>
                @else
                    <a href="{{ route('admin.venues.create') }}"
                        class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="mt-2 block text-sm font-semibold text-gray-900">Add venue</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
