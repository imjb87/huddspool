<section class="ui-section" data-fixture-head-to-head-section>
    <div class="ui-shell-grid">
        <div class="ui-section-intro">
            <div class="ui-section-intro-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ui-section-intro-glyph" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m0 0L13.5 5.25m3 3-3 3m-3 4.5h-9m0 0 3-3m-3 3 3 3" />
                </svg>
            </div>
            <div class="ui-section-intro-copy">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Head to head</h3>
                <p class="mt-1 max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                    Current section standings for the two teams in this fixture.
                </p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="ui-card">
                <div class="ui-card-column-headings px-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3 sm:gap-4"></div>

                    <div class="ml-auto flex shrink-0 items-start gap-3 text-center sm:gap-4">
                        <div class="w-9 sm:w-10">
                            <p class="ui-card-column-header">Pos</p>
                        </div>
                        <div class="w-9 sm:w-10">
                            <p class="ui-card-column-header">Pl</p>
                        </div>
                        <div class="hidden w-9 md:block sm:w-10">
                            <p class="ui-card-column-header">W</p>
                        </div>
                        <div class="hidden w-9 md:block sm:w-10">
                            <p class="ui-card-column-header">D</p>
                        </div>
                        <div class="hidden w-9 md:block sm:w-10">
                            <p class="ui-card-column-header">L</p>
                        </div>
                        <div class="w-9 sm:w-10">
                            <p class="ui-card-column-header">Pts</p>
                        </div>
                    </div>
                </div>

                <div class="ui-card-rows">
                @foreach ($standings as $standing)
                    <a href="{{ route('team.show', $standing->id) }}"
                        class="ui-card-row-link"
                        wire:key="fixture-standing-{{ $standing->id }}">
                        <div class="ui-card-row items-center px-4 sm:px-5">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    <span class="sm:hidden">{{ $standing->shortname ?: $standing->name }}</span>
                                    <span class="hidden sm:inline">{{ $standing->name }}</span>
                                </p>
                            </div>

                            <div class="ml-auto flex shrink-0 items-center gap-3 text-center sm:gap-4">
                                <div class="w-9 sm:w-10">
                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $standing->position }}</p>
                                </div>
                                <div class="w-9 sm:w-10">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->played }}</p>
                                </div>
                                <div class="hidden w-9 md:block sm:w-10">
                                    <p class="text-sm font-semibold text-green-700 dark:text-green-400">{{ $standing->wins }}</p>
                                </div>
                                <div class="hidden w-9 md:block sm:w-10">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->draws }}</p>
                                </div>
                                <div class="hidden w-9 md:block sm:w-10">
                                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">{{ $standing->losses }}</p>
                                </div>
                                <div class="w-9 sm:w-10">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->points }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
