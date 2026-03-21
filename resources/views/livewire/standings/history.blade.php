<section data-history-standings-view>
    <div class="grow-0 overflow-hidden bg-white shadow-sm sm:rounded-lg -mx-4 flex h-full flex-col sm:mx-0 dark:bg-zinc-800/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
        <div class="flex items-center justify-between bg-green-700 px-4 py-4 sm:px-6">
            <h2 class="text-sm font-medium leading-6 text-white">Standings</h2>
            <span class="text-xs font-semibold uppercase tracking-wide text-green-100">{{ $section->name }}</span>
        </div>
        <div class="flex h-full flex-col border-t border-gray-200 dark:border-zinc-800/80">
            <div class="min-w-full overflow-hidden">
                <div class="flex bg-gray-50 dark:bg-zinc-800/70">
                    <div class="flex w-1/2 pl-4 sm:pl-6">
                        <div scope="col" class="w-2/12 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100">#</div>
                        <div scope="col" class="w-10/12 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100">Team</div>
                    </div>
                    <div class="flex w-1/2">
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">Pl</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">W</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">D</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">L</div>
                        <div scope="col" class="w-1/5 py-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">Pts</div>
                    </div>
                </div>
                <div class="bg-white dark:bg-transparent">
                    @if ($standings->isEmpty())
                        <div class="m-4 rounded-lg border-2 border-dashed border-gray-300 p-4 text-center dark:border-zinc-700">
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No standings available for this section yet.</h3>
                            <p class="mx-auto mt-1 max-w-prose text-sm text-gray-500 dark:text-gray-400">
                                Standings will appear once results are entered for this section.
                            </p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-300 dark:divide-zinc-800/80">
                        @foreach ($standingRows as $row)
                            <a class="flex w-full rounded-xl px-4 transition hover:cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-800/70 sm:px-6 {{ $row->withdrawn ? 'line-through' : '' }}"
                                href="{{ route('team.show', $row->id) }}">
                                <div class="flex w-1/2 items-center">
                                    <div class="w-2/12 whitespace-nowrap py-3 text-sm font-semibold {{ $row->text_class }}">
                                        {{ $row->position }}
                                    </div>
                                    <div class="flex w-10/12 flex-col whitespace-nowrap py-3 text-sm {{ $row->text_class }}">
                                        <span class="{{ $row->shortname ? 'hidden md:inline' : '' }}">
                                            {{ $row->name }}
                                        </span>
                                        @if ($row->shortname)
                                            <span class="md:hidden {{ $row->text_class }}">
                                                {{ $row->shortname }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex w-1/2 items-center">
                                    <div class="w-1/5 py-3 text-center text-sm {{ $row->text_class }}">
                                        {{ $row->played }}
                                    </div>
                                    <div class="w-1/5 py-3 text-center text-sm {{ $row->text_class }}">
                                        {{ $row->wins }}
                                    </div>
                                    <div class="w-1/5 py-3 text-center text-sm {{ $row->text_class }}">
                                        {{ $row->draws }}
                                    </div>
                                    <div class="w-1/5 py-3 text-center text-sm {{ $row->text_class }}">
                                        {{ $row->losses }}
                                    </div>
                                    <div class="w-1/5 py-3 text-center text-sm font-semibold {{ $row->points_class }}">
                                        {{ $row->points }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
