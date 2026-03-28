<section class="mt-0 animate-pulse" data-section-tab-skeleton="tables">
    <div class="mx-auto mt-6 w-full max-w-4xl px-4 sm:px-6 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-3 lg:gap-10">
            <div class="space-y-2">
                <div class="h-4 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                <div class="h-4 w-48 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                <div class="h-4 w-40 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            </div>

            <div class="lg:col-span-2">
                <div>
                    <div class="flex items-center justify-between gap-2 pb-0.5 sm:-mx-3 sm:px-3" data-section-table-band>
                        <div class="flex min-w-0 items-center gap-2 sm:gap-3"></div>

                        <div class="ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-3">
                            @foreach (range(1, 5) as $column)
                                <div class="w-8 sm:w-10">
                                    <div class="mx-auto h-3 w-6 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @foreach (range(1, 10) as $row)
                        <div data-section-tab-skeleton-row="tables">
                            <div class="flex items-center justify-between gap-2 rounded-lg py-2 sm:-mx-3 sm:-my-px sm:gap-3 sm:px-3 sm:py-3" data-section-table-band>
                                <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                                    <div class="h-4 w-4 shrink-0 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-36"></div>
                                </div>

                                <div class="ml-auto grid shrink-0 grid-cols-5 gap-2 text-center sm:gap-3">
                                    @foreach (range(1, 5) as $column)
                                        <div class="w-8 sm:w-10">
                                            <div class="mx-auto h-4 w-4 rounded-full bg-gray-200 dark:bg-zinc-700 sm:w-5"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
