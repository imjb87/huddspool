<div>
    <div class="mx-auto max-w-3xl md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl sm:px-6">
        <div class="flex items-center space-x-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $season->name }}
                </h1>
            </div>
        </div>
        <div
            class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
            <a href={{ route('admin.seasons.index') }}
                class="inline-flex items-center justify-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">Back</a>
            <button type="button" wire:click="delete()"
                class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete</button>
            <a href="{{ route('admin.seasons.edit', $season) }}"
                class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Edit</a>
        </div>
    </div>

    <div
        class="mx-auto mt-8 grid max-w-3xl grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2 lg:col-start-1">
            <!-- Description list-->
            <section>
                <div class="bg-white shadow rounded sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Season
                            Information</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic information about
                            the season.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Start date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ date('d/m/Y', strtotime($season->dates[0])) }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">End date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ date('d/m/Y', strtotime($season->dates[count($season->dates) - 1])) }}
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if ($season->is_open)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Open
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Closed
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>
            <section>
                <div class="bg-white shadow rounded sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex flex-col sm:flex-row">
                        <div class="flex-1">
                            <h2 class="text-lg font-medium leading-6 text-gray-900">Sections</h2>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">List of sections in the
                                season.</p>
                        </div>
                        <a href="{{ route('admin.sections.create', $season) }}"
                            class="sm:self-end sm:justify-self-end rounded-md bg-indigo-600 py-2 px-3 mt-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                            section</a>

                    </div>
                    <div class="border-t border-gray-200">
                        @if ($season->sections->count() > 0)
                            <div class="overflow-hidden rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                Name</th>
                                            <th scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 hidden sm:table-cell">
                                                Ruleset</th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($season->sections as $section)
                                            <tr>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ $section->name }}</td>
                                                <td
                                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 hidden sm:table-cell">
                                                    {{ $section->ruleset->name }}
                                                </td>
                                                <td
                                                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                    <a class="rounded bg-indigo-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        href="{{ route('admin.sections.show', $section) }}">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-4 sm:px-6 py-5">
                                <a href="{{ route('admin.sections.create', $season) }}"
                                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="mt-2 block text-sm font-semibold text-gray-900">Add section</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
            <section>
<<<<<<< HEAD
                <div class="bg-white shadow rounded sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex flex-col sm:flex-row">
                        <div class="flex-1">
                            <h2 class="text-lg font-medium leading-6 text-gray-900">Knockouts</h2>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">List of knockouts in the
                                season.</p>
                        </div>
                        <a href="{{ route('admin.knockouts.create', $season) }}"
                            class="sm:self-end sm:justify-self-end rounded-md bg-indigo-600 py-2 px-3 mt-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                            knockout</a>

                    </div>
                    <div class="border-t border-gray-200">
                        @if ($season->knockouts->count() > 0)
=======
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 flex flex-col sm:flex-row">
                        <div class="flex-1">
                            <h2 class="text-lg font-medium leading-6 text-gray-900">Expulsions</h2>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">List of expulsions in the
                                season.</p>
                        </div>
                        <a href="{{ route('admin.expulsions.create', $season) }}"
                            class="sm:self-end sm:justify-self-end rounded-md bg-indigo-600 py-2 px-3 mt-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add an expulsion</a>
                    </div>
                    <div class="border-t border-gray-200">
                        @if ($season->expulsions->count() > 0)
>>>>>>> 12743037494432b37f8d22ec27d16f2bb5a7e8e4
                            <div class="overflow-hidden rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                Name</th>
<<<<<<< HEAD
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($season->knockouts as $knockout)
                                            <tr>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ $knockout->name }}</td>
                                                <td
                                                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                    <a class="rounded bg-indigo-600 px-2 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                                        href="{{ route('admin.knockouts.show', $knockout) }}">View</a>
                                                </td>
=======
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($season->expulsions as $expulsion)
                                            <tr>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ $expulsion->expellable->name }}</td>
>>>>>>> 12743037494432b37f8d22ec27d16f2bb5a7e8e4
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-4 sm:px-6 py-5">
<<<<<<< HEAD
                                <a href="{{ route('admin.knockouts.create', $season) }}"
                                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="mt-2 block text-sm font-semibold text-gray-900">Add knockout</span>
=======
                                <a href="{{ route('admin.expulsions.create', $season) }}"
                                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="mt-2 block text-sm font-semibold text-gray-900">Add expulsion</span>
>>>>>>> 12743037494432b37f8d22ec27d16f2bb5a7e8e4
                                </a>
                            </div>
                        @endif
                    </div>
<<<<<<< HEAD
                </div>                
            </section>
=======
                </div>
            </section>            
>>>>>>> 12743037494432b37f8d22ec27d16f2bb5a7e8e4
        </div>

        <section class="lg:col-span-1 lg:col-start-3">
            <div class="bg-white shadow rounded sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6">
                    <h2 class="text-lg font-medium leading-6 text-gray-900">Schedule</h2>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">List of dates in the
                        season.</p>
                </div>
                <div class="border-t border-gray-200">
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach ($season->dates as $key => $date)
                            <li
                                class="relative bg-white py-3 px-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 hover:bg-gray-50">
                                <div class="flex justify-between space-x-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900">Week {{ $key + 1 }}
                                        </p>
                                    </div>
                                    <time datetime="2021-01-27T16:35"
                                        class="flex-shrink-0 whitespace-nowrap text-sm text-gray-500">{{ date('d/m/Y', strtotime($date)) }}</time>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>        
    </div>
</div>
