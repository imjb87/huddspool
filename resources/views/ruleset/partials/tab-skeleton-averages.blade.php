<section class="mt-0 animate-pulse" data-section-tab-skeleton="averages">
    <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <div class="h-4 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                <div class="h-4 w-48 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            </div>

            <div class="lg:col-span-2">
                <div>
                    <div class="flex items-center justify-between gap-2 pb-0.5 sm:-mx-3 sm:px-3" data-section-averages-band>
                        <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                        <div class="ml-auto flex shrink-0 items-start gap-2 text-center sm:gap-5">
                            @foreach (range(1, 3) as $column)
                                <div class="w-12 sm:w-16">
                                    <div class="mx-auto h-3 w-8 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @foreach (range(1, 5) as $row)
                        <div data-section-tab-skeleton-row="averages">
                            <div class="flex items-center gap-3 rounded-lg py-3 sm:-mx-3 sm:-my-px sm:gap-4 sm:px-3 sm:py-4" data-section-averages-band>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="h-4 w-4 shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                        <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 w-32 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                            <div class="h-3 w-16 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-5">
                                    <div class="w-12 sm:w-16">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                            <div class="h-5 w-12 rounded-md opacity-0"></div>
                                        </div>
                                    </div>
                                    <div class="w-12 sm:w-16">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                            <div class="h-5 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                        </div>
                                    </div>
                                    <div class="w-12 sm:w-16">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-10"></div>
                                            <div class="h-5 w-12 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="pt-5 pb-4 lg:pt-5 lg:pb-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            <div class="h-4 w-14 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
