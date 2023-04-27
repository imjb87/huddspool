<div>
    <div
        class="mx-auto max-w-3xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
        <div class="flex items-center space-x-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}
                </h1>
            </div>
        </div>
        <div
            class="justify-stretch mt-6 flex flex-col-reverse space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-y-0 sm:space-x-3 sm:space-x-reverse md:mt-0 md:flex-row md:space-x-3">
            <a class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                href="{{ route('admin.sections.show', $fixture->section) }}">
                Back to section</a>
            @if (!$fixture->result)
                <button type="button" wire:click="deleteFixture()"
                    class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete
                    fixture</button>
                <a
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Edit
                    fixture</a>
            @else
                <button type="button" wire:click="deleteResult()"
                    class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">Delete
                    result</button>
                <a
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Edit
                    result</a>
            @endif
        </div>

    </div>

    <div
        class="mx-auto mt-8 grid max-w-3xl grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-3 lg:col-start-1">
            <!-- Description list-->
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Fixture
                            Information</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">General
                            information about this fixture.</p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Venue</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $fixture->venue->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ date('d/m/Y', strtotime($fixture->fixture_date)) }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Section</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $fixture->section->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Season</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $fixture->section->season->name }}</dd>
                            </div>

                        </dl>
                    </div>
                </div>
            </section>
            <section>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg font-medium leading-6 text-gray-900">Result</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Result of this fixture.</p>
                    </div>
                    <div class="border-t border-gray-200">
                        @if ($fixture->result)
                            <div class="overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                                <div class="bg-gray-50 hidden sm:flex">
                                    <div
                                        class="flex-1 leading-6 py-2 px-4 sm:px-6 text-right text-sm font-semibold text-gray-900">
                                        {{ $fixture->homeTeam->name }}
                                    </div>
                                    <div class="w-12 text-center text-sm leading-6 py-2 font-semibold text-gray-900">
                                        vs
                                    </div>
                                    <div
                                        class="flex-1 leading-6 py-2 px-4 sm:px-6 text-left text-sm font-semibold text-gray-900">
                                        {{ $fixture->awayTeam->name }}
                                    </div>
                                </div>
                                @foreach ($fixture->result->frames as $key => $frame)
                                    <div class="flex flex-wrap">
                                        <div
                                            class="w-full sm:w-auto flex sm:flex-1 order-2 sm:order-first border-b border-gray-200 sm:border-0">
                                            <div
                                                class="border-0 py-1.5 px-4 sm:px-6 leading-6 text-sm flex-1 focus:outline-0 focus:ring-0">
                                                {{ $frame->homePlayer->name ?? 'Awarded' }}
                                            </div>
                                            <div class="w-10 sm:w-12 border-x border-gray-200">
                                                <div
                                                    class="block w-full border-0 pr-0 pl-0 py-1.5 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0">
                                                    {{ $frame->home_score }}
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="w-full sm:w-12 sm:text-center bg-gray-50 py-1.5 px-4 sm:px-0 text-left text-sm font-semibold text-gray-900 order-first sm:order-2 leading-6">
                                            <span class="sm:hidden">Frame </span>
                                            {{ $key + 1 }}
                                        </div>
                                        <div class="w-full sm:w-auto flex sm:flex-1 order-last">
                                            <div
                                                class="w-10 sm:w-12 order-last sm:order-first border-x border-gray-200">
                                                <div
                                                    class="block w-full border-0 pr-0 pl-0 py-1.5 leading-6 text-gray-900 text-sm text-center focus:outline-0 focus:ring-0">
                                                    {{ $frame->away_score }}
                                                </div>
                                            </div>
                                            <div
                                                class="border-0 py-1.5 px-4 sm:px-6 leading-6 text-sm flex-1 order-first sm:order-last focus:outline-0 focus:ring-0">
                                                {{ $frame->awayPlayer->name ?? 'Awarded' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="flex flex-wrap bg-gray-50 font-semibold text-gray-900 text-sm">
                                    <div class="w-full sm:w-auto flex sm:flex-1 border-b border-gray-200">
                                        <div class="flex-1 leading-6 py-1.5 px-4 sm:px-6 sm:text-right">
                                            Home total
                                        </div>
                                        <div class="w-10 sm:w-12 leading-6 py-1.5 text-center border-x border-gray-200">
                                            {{ $fixture->result->home_score }}
                                        </div>
                                    </div>
                                    <div class="w-10 sm:w-12 bg-gray-50"></div>
                                    <div class="w-full sm:w-auto flex sm:flex-1">
                                        <div
                                            class="w-10 sm:w-12 leading-6 py-1.5 text-center border-x border-gray-200 order-last sm:order-first">
                                            {{ $fixture->result->away_score }}
                                        </div>
                                        <div class="flex-1 leading-6 py-1.5 px-4 sm:px-6 order-first sm:order-last">
                                            Away total
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="px-6 py-4">
                                <a href="{{ route('admin.results.create', $fixture) }}"
                                    class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="mt-2 block text-sm font-semibold text-gray-900">Add result</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
