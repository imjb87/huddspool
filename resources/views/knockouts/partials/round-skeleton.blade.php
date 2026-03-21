<section class="mx-auto mt-6 w-full max-w-4xl animate-pulse px-4 sm:px-6 lg:px-6">
    <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
        <div class="space-y-2">
            <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
        </div>

        <div class="lg:col-span-2">
            <div class="divide-y divide-gray-200 dark:divide-zinc-800/80">
                @foreach (range(1, 5) as $row)
                    <div class="py-4" data-knockout-round-skeleton-row>
                        <div class="flex items-start justify-between gap-4" data-section-fixtures-band>
                            <div class="min-w-0 flex-1">
                                <div class="space-y-2">
                                    <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-4 w-36 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>

                                <div class="mt-2 flex items-center gap-2">
                                    <div class="h-3 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-3 w-3 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-3 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            </div>

                            <div class="h-7 w-[60px] shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-5 pb-4 lg:pt-5 lg:pb-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    <div class="h-4 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
            </div>
        </div>
    </div>
</section>
