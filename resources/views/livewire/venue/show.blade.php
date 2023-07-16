<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-3xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">{{ $venue->name }}</h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-wrap lg:flex-nowrap gap-x-6 gap-y-4">
                <div class="w-full lg:w-1/3 flex flex-col gap-4">
                    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                        <dl class="flex flex-wrap">
                            <div class="flex-auto pl-6 pt-6">
                                <dt class="text-sm font-semibold leading-6 text-gray-900">Address</dt>
                                <dd class="mt-1 text-sm leading-6 text-gray-900">
                                    {{ $venue->address }}
                                </dd>
                            </div>
                            <div class="mt-6 flex w-full flex-none gap-x-4 border-t border-gray-900/5 px-6 py-6">
                                <dt class="flex w-5">
                                    <span class="sr-only">Venue telephone</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 text-gray-400 mx-auto"
                                        viewBox="0 0 512 512" fill="currentColor">
                                        <path
                                            d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
                                    </svg>
                                </dt>
                                <dd class="text-sm leading-6 text-gray-900">
                                    <a href="tel:{{ $venue->telephone }}"
                                        class="text-sm font-medium leading-6 text-gray-900 hover:underline">{{ $venue->telephone }}</a>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div class="bg-white shadow rounded-md sm:rounded-lg overflow-hidden">
                        <div class="px-4 py-4 lg:px-6 bg-green-700">
                            <h2 class="text-sm font-medium leading-6 text-white">Teams</h2>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                Name</th>
                                            <th scope="col"
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Captain
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($venue->teams as $team)
                                            <tr>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    <a class="hover:underline"
                                                        href="{{ route('team.show', $team->id) }}">
                                                        {{ $team->name }}
                                                    </a>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    <a class="hover:underline"
                                                        href="{{ route('player.show', $team->captain->id ?? 0) }}">
                                                        {{ $team->captain?->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>                    
                </div>
                <section class="w-full lg:w-2/3">
                    <iframe class="w-full shadow-md sm:rounded-lg"
                        src="https://www.google.com/maps/embed/v1/place?q={{ $venue->address }}&key=AIzaSyChqGQscjoMavQG46mrE3j2oz26pedhXFU"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </section>
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>
