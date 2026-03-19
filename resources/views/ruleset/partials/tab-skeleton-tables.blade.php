<section class="mt-0 animate-pulse" data-section-tab-skeleton="tables">
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md dark:border-zinc-800/80 dark:bg-zinc-900/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
        <div class="border-b border-gray-200 bg-linear-to-b from-gray-50 to-gray-100 dark:border-zinc-800/80 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800/80">
            <div class="mx-auto flex w-full max-w-4xl">
                <div class="flex w-[44%] pl-4 sm:w-1/2 sm:pl-6">
                    <div class="w-2/12 py-2">
                        <div class="h-4 w-5 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    </div>
                    <div class="w-10/12 py-2">
                        <div class="h-4 w-20 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                    </div>
                </div>
                <div class="flex w-[56%] pr-4 sm:w-1/2 sm:pr-6">
                    @foreach (range(1, 5) as $column)
                        <div class="w-1/5 py-2 text-right">
                            <div class="ml-auto h-4 w-6 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900/75">
            @foreach (range(1, 10) as $row)
                <div class="border-t border-gray-300 dark:border-zinc-800/80" data-section-tab-skeleton-row="tables">
                    <div class="mx-auto flex w-full max-w-4xl">
                        <div class="flex w-[44%] items-center pl-4 sm:w-1/2 sm:pl-6">
                            <div class="w-2/12 py-2">
                                <div class="h-4 w-4 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            </div>
                            <div class="w-10/12 py-2">
                                <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                            </div>
                        </div>
                        <div class="flex w-[56%] items-center pr-4 sm:w-1/2 sm:pr-6">
                            @foreach (range(1, 5) as $column)
                                <div class="w-1/5 py-2 text-right">
                                    <div class="ml-auto h-4 w-6 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
