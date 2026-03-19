<section class="mt-0 animate-pulse">
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md">
        <div class="border-b border-gray-300 bg-linear-to-b from-gray-50 to-gray-100">
            <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3 px-4 py-2 sm:px-6">
                <div class="h-4 w-24 rounded-full bg-gray-200"></div>
                <div class="h-4 w-28 rounded-full bg-gray-200"></div>
            </div>
        </div>

        <div class="bg-white">
            @foreach (range(1, 5) as $row)
                <div class="border-t border-gray-300" data-knockout-round-skeleton-row>
                    <div class="mx-auto flex w-full max-w-4xl flex-col gap-3 px-4 py-4 sm:px-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="h-3 w-16 rounded-full bg-gray-200"></div>
                            <div class="h-3 w-20 rounded-full bg-gray-200"></div>
                        </div>

                        <div class="grid grid-cols-[minmax(0,1fr)_88px_minmax(0,1fr)] items-center gap-3">
                            <div class="ml-auto h-4 w-28 rounded-full bg-gray-200"></div>
                            <div class="mx-auto h-7 w-[44px] rounded-sm bg-gray-200"></div>
                            <div class="h-4 w-28 rounded-full bg-gray-200"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl px-4 py-4 sm:px-6 lg:py-6">
        <div class="flex w-full">
            <div class="flex w-[41%] justify-start">
                <div class="h-10 w-24 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex w-[18%] items-center justify-center">
                <div class="h-4 w-24 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex w-[41%] justify-end">
                <div class="h-10 w-24 rounded-full bg-gray-200"></div>
            </div>
        </div>
    </div>
</section>
