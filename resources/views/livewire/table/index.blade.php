<div>
    <div class="bg-white pt-24 sm:pt-32 pb-8 sm:pb-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-4xl font-serif">{{ $ruleset->name }} Tables</h2>
            </div>
        </div>
    </div>
    <div class="py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-6 gap-y-6">
                @foreach ($sections as $section)
                    <section>
                        <div class="bg-white shadow-md rounded-md sm:rounded-lg overflow-hidden">
                            <div class="px-4 py-4 bg-green-700">
                                <h2 class="text-sm font-medium leading-6 text-white">{{ $section->name }}</h2>
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
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900">Pl
                                            </th>
                                            <th scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:table-cell">W
                                            </th>
                                            <th scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:table-cell">D</th>
                                            <th scope="col"
                                                class="px-2 py-2 text-center text-sm font-semibold text-gray-900 hidden md:table-cell">L</th>
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
                                                    class="px-2 sm:px-3 py-2 text-sm font-medium text-gray-900 truncate max-w-[12ch] sm:max-w-[16ch] md:max-w-auto">
                                                    <a class="hover:underline {{ $team->shortname ? "hidden md:inline" : "" }}" href="{{ route('team.show', $team->id) }}">{{ $team->name }}</a>
                                                    @if ($team->shortname)
                                                        <a class="md:hidden" href="{{ route('team.show', $team->id) }}">{{ $team->shortname }}</span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center">
                                                    {{ $team->played }}</td>
                                                <td
                                                    class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center hidden md:table-cell">
                                                    {{ $team->wins }}</td>
                                                <td
                                                    class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center hidden md:table-cell">
                                                    {{ $team->draws }}</td>
                                                <td
                                                    class="whitespace-nowrap px-2 py-2 text-sm text-gray-500 font-semibold text-center hidden md:table-cell">
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
                @endforeach
            </div>
        </div>
    </div>
    <x-logo-clouds />
</div>