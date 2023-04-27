<div>
    <div
        class="mx-auto max-w-3xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
        <div class="flex items-center space-x-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $venue->name }}
                </h1>
            </div>
        </div>
        <div
            class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
            <button type="button" wire:click="delete()"
                class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete</button>
            <a href="{{ route('admin.venues.edit', $venue) }}"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Edit</a>
        </div>
    </div>

    <div
        class="mx-auto mt-8 grid max-w-3xl grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2 lg:col-start-1">
            <!-- Description list-->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Venue
                            Information</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Venue details and
                            information.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $venue->address }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Telephone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $venue->telephone }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Teams</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Teams that play at
                            this venue.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <div class="overflow-hidden bg-white shadow sm:rounded-md">
                            <ul role="list" class="divide-y divide-gray-200">
                                @foreach ($venue->teams as $team)
                                    <li>
                                        <a class="block hover:bg-gray-50" href="{{ route('admin.teams.show', $team) }}">
                                            <div class="flex items-center px-4 py-4 sm:px-6">
                                                <div class="flex min-w-0 flex-1 items-center">
                                                    <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                                        <div>
                                                            <p class="truncate text-sm font-medium text-gray-600">
                                                                {{ $team->name }}</p>
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
                    </div>
                </div>
            </section>
        </div>

        <section class="lg:col-span-1 lg:col-start-3">
            <iframe class="w-full shadow sm:rounded-lg"
                src="https://www.google.com/maps/embed/v1/place?q={{ $venue->address }}&key=AIzaSyChqGQscjoMavQG46mrE3j2oz26pedhXFU"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </section>
    </div>
</div>
