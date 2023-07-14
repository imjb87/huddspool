<div>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5">
        <div class="flex items-center space-x-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $section->name }}
                </h1>
            </div>
        </div>
        <div
            class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
            @if ($section->fixtures->count() > 0 && $section->results->count() == 0)
                <a type="button" href="{{ route('admin.fixtures.edit', $section) }}"
                    class="inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Edit
                    fixtures</a>
                <button type="button" wire:click="regenerateFixtures()"
                    class="inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Regenerate
                    fixtures</button>
            @endif
            <a href="{{ route('admin.seasons.show', $section->season) }}"
                class="inline-flex items-center justify-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">Back
                to season</a>
            <button type="button" wire:click="delete()"
                class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete</button>
            <a href="{{ route('admin.sections.edit', $section) }}"
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
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Section
                            Information</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic information about
                            the section.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Ruleset</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $section->ruleset->name }}</dd>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Season</dt>
                                <dd class="mt-1 text-sm text-gray-900"><a class="font-semibold hover:underline"
                                        href="{{ route('admin.seasons.show', $section->season) }}">{{ $section->season->name }}</a>
                                </dd>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>
            <div class="grid lg:grid-cols-1 gap-y-6">
            <section class="lg:col-span-3">
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Standings</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Current standings for
                            the section.</p>
                    </div>
                    <div class="border-t border-gray-200">
                        <table class="w-full max-w-full overflow-hidden">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="px-2 py-2 sm:px-3 text-left text-sm font-semibold text-gray-900">
                                        Team</th>
                                    <th scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900">P
                                    </th>
                                    <th scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900">W
                                    </th>
                                    <th scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900">D</th>
                                    <th scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900">L</th>
                                    <th scope="col"
                                        class="px-2 py-2 text-center text-sm font-semibold text-gray-900">Pts</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach ($section->standings() as $team)
                                    <tr class="border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50">
                                        <td
                                            class="whitespace-nowrap py-2 px-2 text-sm font-medium text-gray-900 text-center">
                                                {{ $loop->iteration }}
                                        </td>
                                        <td
                                            class="whitespace-nowrap px-2 sm:px-3 py-2 text-sm font-medium text-gray-900">
                                            {{ $team->name }}</td>
                                        <td
                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center">
                                            {{ $team->played }}</td>
                                        <td
                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center">
                                            {{ $team->wins }}</td>
                                        <td
                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center">
                                            {{ $team->draws }}</td>
                                        <td
                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center">
                                            {{ $team->losses }}</td>
                                        <td
                                            class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center">
                                            {{ $team->points }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <section class="lg:col-span-2">
                <div class="bg-white shadow sm:rounded-lg flex flex-col h-full">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Fixtures</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">The fixture list for this
                            section.</p>
                    </div>
                    <div class="border-t border-gray-200 h-full flex flex-col">
                        @if ($section->fixtures->count() > 0)
                            <table class="min-w-full overflow-hidden">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-2 py-2 text-right text-sm font-semibold text-gray-900">Home
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-2 text-center text-sm font-semibold text-gray-900"></th>
                                        <th scope="col"
                                            class="px-2 py-2 text-center text-sm font-semibold text-gray-900"></th>
                                        <th scope="col"
                                            class="px-2 py-2 text-left text-sm font-semibold text-gray-900">Away
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">

                                    @foreach ($fixtures as $fixture)
                                        <tr class="border-t border-gray-300 hover:cursor-pointer hover:bg-gray-50"
                                            wire:click="showFixture({{ $fixture }})">
                                            <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-right">
                                                {{ $fixture->homeTeam->name }}</td>
                                            @if ($fixture->result)
                                                <td
                                                    class="whitespace-nowrap px-1 py-4 text-sm text-gray-500 text-right font-semibold">
                                                    <span
                                                        class="inline-block bg-green-700 text-white rounded-md w-6 text-center">{{ $fixture->result->home_score ?? '' }}</span>
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-1 py-4 text-sm text-gray-500 font-semibold">
                                                    <span
                                                        class="inline-block bg-green-700 text-white rounded-md w-6 text-center">{{ $fixture->result->away_score ?? '' }}</span>
                                                </td>
                                            @else
                                                <td colspan="2"
                                                    class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-center font-semibold">
                                                    {{ $fixture->fixture_date->format('d/m') }}</td>
                                            @endif
                                            </td>
                                            <td class="whitespace-nowrap px-2 py-4 text-sm text-gray-500 text-left">
                                                {{ $fixture->awayTeam->name }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="py-4 px-6 mt-auto">
                                {{ $fixtures->links() }}
                            </div>
                        @else
                            <div class="px-6 py-4">
                                <a href="{{ route('admin.fixtures.create', $section) }}"
                                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="mt-2 block text-sm font-semibold text-gray-900">Generate
                                        fixtures</span>
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
