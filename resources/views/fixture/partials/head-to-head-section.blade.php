<section class="border-t border-gray-200 pt-6 dark:border-zinc-800/80" data-fixture-head-to-head-section>
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Head to head</h3>
            <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">
                Current section standings for the two teams in this fixture.
            </p>
        </div>

        <div class="lg:col-span-2">
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                @foreach ($standings as $standing)
                    <a href="{{ route('team.show', $standing->id) }}"
                        class="block"
                        wire:key="fixture-standing-{{ $standing->id }}">
                        <div class="flex items-center gap-4 py-4">
                            <div class="w-8 shrink-0 text-sm font-semibold text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->name }}</p>
                            </div>

                            <div class="ml-auto flex shrink-0 items-center gap-5 text-center">
                                <div class="w-12">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pl</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->played }}</p>
                                </div>
                                <div class="hidden w-12 md:block">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">W</p>
                                    <p class="mt-1 text-sm font-semibold text-green-700 dark:text-green-400">{{ $standing->wins }}</p>
                                </div>
                                <div class="hidden w-12 md:block">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">D</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->draws }}</p>
                                </div>
                                <div class="hidden w-12 md:block">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">L</p>
                                    <p class="mt-1 text-sm font-semibold text-red-700 dark:text-red-400">{{ $standing->losses }}</p>
                                </div>
                                <div class="w-12">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pts</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $standing->points }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
