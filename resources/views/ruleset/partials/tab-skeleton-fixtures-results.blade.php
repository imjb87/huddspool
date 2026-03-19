<section class="mt-0 animate-pulse" data-section-tab-skeleton="fixtures-results">
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md dark:border-zinc-800/80 dark:bg-zinc-900/75 dark:shadow-none dark:ring-1 dark:ring-white/5">
        <div class="border-b border-gray-200 bg-linear-to-b from-gray-50 to-gray-100 dark:border-zinc-800/80 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800/80">
            <div class="mx-auto flex w-full max-w-4xl">
                <div class="w-[41%] py-2 pl-4 text-right sm:pl-6">
                    <div class="ml-auto h-4 w-12 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
                <div class="w-[18%] px-1 py-2 text-center">
                    <div class="mx-auto h-4 w-8 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
                <div class="w-[41%] py-2 pr-4 text-left sm:pr-6">
                    <div class="h-4 w-12 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                </div>
            </div>
        </div>

        <div class="border-b border-gray-300 bg-white dark:border-zinc-800/80 dark:bg-zinc-900/75">
            @foreach (range(1, 5) as $row)
                <div class="border-t border-gray-300 dark:border-zinc-800/80" data-section-tab-skeleton-row="fixtures-results">
                    <div class="mx-auto flex w-full max-w-4xl items-center">
                        <div class="w-[41%] py-4 pl-4 text-right sm:pl-6">
                            <div class="ml-auto h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="flex w-[18%] items-center justify-center px-1 py-3">
                            <div class="h-7 w-[44px] rounded-sm bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="w-[41%] py-4 pr-4 text-left sm:pr-6">
                            <div class="h-4 w-28 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl px-4 py-4 sm:px-6 lg:px-6 lg:py-6">
        <div class="flex w-full">
            <div class="flex w-[41%] justify-start">
                <div class="h-10 w-24 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            </div>
            <div class="flex w-[18%] items-center justify-center">
                <div class="h-4 w-14 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            </div>
            <div class="flex w-[41%] justify-end">
                <div class="h-10 w-16 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
            </div>
        </div>
    </div>
</section>
