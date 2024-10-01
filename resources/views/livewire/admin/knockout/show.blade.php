<div>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5">
        <div class="flex items-center space-x-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $knockout->name }}
                </h1>
            </div>
        </div>
        <div
            class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">

            <a href="{{ route('admin.seasons.show', $knockout->season) }}"
                class="inline-flex items-center justify-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">Back
                to season</a>
            <button type="button" wire:click="delete()"
                class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete</button>
            <a href="{{ route('admin.knockouts.edit', $knockout) }}"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Edit</a>
        </div>
    </div>

    <div
        class="mx-auto mt-8 grid max-w-4xl grid-cols-1 gap-6 sm:px-6 lg:grid-flow-col-dense lg:grid-cols-4">
        <div class="space-y-6 lg:col-span-4 lg:col-start-1">
            <!-- Description list-->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Knockout
                            Information</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic information about
                            the knockout.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Knockout type</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ ucfirst($knockout->type) }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Season</dt>
                                <dd class="mt-1 text-sm text-gray-900"><a class="font-semibold hover:underline"
                                        href="{{ route('admin.seasons.show', $knockout->season) }}">{{ $knockout->season->name }}</a>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>
            <div class="grid lg:grid-cols-1 gap-y-6">

            <section>
                <div class="bg-white shadow rounded sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex flex-col sm:flex-row">
                        <div class="flex-1">
                            <h2 class="text-lg font-medium leading-6 text-gray-900">Rounds</h2>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">List of rounds in the
                                knockout.</p>
                        </div>
                        <a href="{{ route('admin.rounds.create', $knockout) }}"
                            class="sm:self-end sm:justify-self-end rounded-md bg-indigo-600 py-2 px-3 mt-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                            a round</a>

                    </div>
                    <div class="border-t border-gray-200">
                        @if ($knockout->rounds->count() > 0)
                            <div class="overflow-hidden rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                Name</th>
                                            <th scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                Date</th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($knockout->rounds as $round)
                                            <tr>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ $round->name }}</td>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ date('d/m/Y', strtotime($round->date)) }}</td>
                                                <td
                                                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                    <a class="rounded bg-indigo-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        href="{{ route('admin.rounds.show', $round) }}">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-4 sm:px-6 py-5">
                                <a href="{{ route('admin.rounds.create', $knockout) }}"
                                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="mt-2 block text-sm font-semibold text-gray-900">Add a round</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>                
            </section>
        </div>
        </div>
    </div>
</div>
