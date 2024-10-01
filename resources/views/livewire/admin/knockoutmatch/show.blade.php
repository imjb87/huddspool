<div>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5">
        <div class="flex items-center space-x-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $match->title }}
                </h1>
            </div>
        </div>
        <div
            class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
            <a href="{{ route('admin.rounds.show', $match->round) }}"
                class="whitespace-nowrap inline-flex items-center justify-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">Back
                to round</a>
            <button type="button" wire:click="delete()"
                class="whitespace-nowrap inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete</button>
            <a href="{{ route('admin.matches.edit', $match) }}"
                class="whitespace-nowrap inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Edit</a>
        </div>
    </div>

    <div
        class="mx-auto mt-8 grid max-w-4xl grid-cols-1 gap-6 sm:px-6 lg:grid-flow-col-dense lg:grid-cols-4">
        <div class="space-y-6 lg:col-span-4 lg:col-start-1">
            <!-- Description list-->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Match
                            Information</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic information about
                            the match.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Knockout</dt>
                                <dd class="mt-1 text-sm text-gray-900"><a class="font-semibold hover:underline"
                                        href="{{ route('admin.knockouts.show', $match->round->knockout) }}">{{ $match->round->knockout->name }}</a>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Round</dt>
                                <dd class="mt-1 text-sm text-gray-900"><a class="font-semibold hover:underline"
                                        href="{{ route('admin.rounds.show', $match->round) }}">{{ $match->round->name }}</a>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Venue</dt>
                                <dd class="mt-1 text-sm text-gray-900"><a class="font-semibold hover:underline"
                                        href="{{ route('admin.venues.show', $match->venue) }}">{{ $match->venue->name }}</a>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ date('d/m/Y', strtotime($match->round->date)) }}</dd>
                            </div>       
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Best of</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $match->round->best_of }}</dd>
                            </div>                                                        
                        </dl>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
