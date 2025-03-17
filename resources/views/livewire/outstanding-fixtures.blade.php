<div class="bg-gray-100 dark:bg-gray-800 rounded-lg duration-500 p-4 md:p-6 shadow mb-4 md:mb-6">
    <h2 class="dark:text-white text-[10px] font-semibold duration-500 uppercase tracking-widest">Outstanding Fixtures</h2>
    <ul class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
        @foreach( $fixtures as $fixture )
            <li>
            <a class="group dark:text-white duration-500 hover:bg-gray-300 dark:hover:bg-gray-700 flex py-2 items-center rounded-lg px-3 -mx-3 -my-[1px]" href="{{ route('fixture.show', $fixture) }}">
                <div class="min-w-[80px]">
                    <span class="text-gray-500 text-[10px] uppercase tracking-widest">{{ $fixture->fixture_date->format('d.m.Y') }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="dark:text-white text-xs duration-500">{{ $fixture->homeTeam->name }}</span>
                    <span class="dark:text-white text-xs duration-500">{{ $fixture->awayTeam->name }}</span>
                </div>
                <div class="flex flex-col gap-y-1 ml-auto">
                    <div class="rounded-lg p-2 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-900 duration-500">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </div>
                </div>
            </a>
            </li>
        @endforeach
    </ul>
    <div class="mt-4">
        {{ $fixtures->links() }}
    </div>
</div>
