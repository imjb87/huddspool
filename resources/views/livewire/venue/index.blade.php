<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">Venues</h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="my-4 sm:flex-none w-full lg:max-w-sm lg:ml-auto">
                <div class="relative">
                    <input type="text" name="search" id="search" wire:model="search"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        placeholder="Start typing to search..." />
                </div>
            </div>            
            <div class="overflow-hidden bg-white shadow rounded-md lg:rounded-lg">
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach ($venues as $venue)
                        <li>
                            <a class="block hover:bg-gray-50" href="{{ route('venue.show', $venue) }}">
                                <div class="flex items-center px-4 py-4 sm:px-6">
                                    <div class="flex min-w-0 flex-1 items-center">
                                        <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
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
                @if (count($venues) > 9)
                <div class="p-4 lg:px-8">
                    {{ $venues->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>
