<section class="mt-0 animate-pulse" data-section-tab-skeleton="averages">
    <div class="w-full overflow-hidden border-y border-gray-200 bg-white shadow-md">
        <div class="bg-linear-to-b from-gray-50 to-gray-100">
            <div class="mx-auto flex w-full max-w-4xl">
                <div class="flex w-[56%] pl-4 sm:w-1/2 sm:pl-6">
                    <div class="w-2/12 py-2">
                        <div class="h-4 w-5 rounded-full bg-gray-200"></div>
                    </div>
                    <div class="w-10/12 py-2">
                        <div class="h-4 w-20 rounded-full bg-gray-200"></div>
                    </div>
                </div>
                <div class="grid w-[44%] grid-cols-3 pr-4 sm:w-1/2 sm:pr-0">
                    @foreach (range(1, 3) as $column)
                        <div class="py-2 text-center">
                            <div class="mx-auto h-4 w-6 rounded-full bg-gray-200"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white">
            @foreach (range(1, 10) as $row)
                <div class="border-t border-gray-300" data-section-tab-skeleton-row="averages">
                    <div class="mx-auto flex w-full max-w-4xl items-center">
                        <div class="flex w-[56%] items-center pl-4 sm:w-1/2 sm:pl-6">
                            <div class="w-2/12 py-2">
                                <div class="h-4 w-4 rounded-full bg-gray-200"></div>
                            </div>
                            <div class="flex w-10/12 items-center gap-3 py-2">
                                <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                <div class="h-4 w-32 rounded-full bg-gray-200"></div>
                            </div>
                        </div>
                        <div class="grid w-[44%] grid-cols-3 items-center pr-4 sm:w-1/2 sm:pr-0">
                            <div class="py-2 text-center">
                                <div class="mx-auto h-4 w-6 rounded-full bg-gray-200"></div>
                            </div>
                            <div class="py-2 text-center">
                                <div class="mx-auto h-4 w-16 rounded-full bg-gray-200"></div>
                            </div>
                            <div class="py-2 text-center">
                                <div class="mx-auto h-4 w-6 rounded-full bg-gray-200"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mx-auto w-full max-w-4xl px-4 py-4 sm:px-6 lg:px-6 lg:py-6">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
            <div class="justify-self-start h-10 w-24 rounded-full bg-gray-200"></div>
            <div class="h-4 w-14 rounded-full bg-gray-200"></div>
            <div class="justify-self-end h-10 w-16 rounded-full bg-gray-200"></div>
        </div>
    </div>
</section>
