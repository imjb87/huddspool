<div class="w-60 py-6 h-full hidden md:block">
<aside class="w-60 rounded-lg p-4 md:p-6 bg-gray-100 dark:bg-gray-800 duration-500 shadow">
    <h2 class="dark:text-white text-[10px] px-3 font-semibold -mx-3 duration-500 uppercase tracking-widest">Sections</h2>
    <ul class="mt-3">
        @foreach( $season->sections as $section )
            <li>
                <a class="group dark:text-white duration-500 hover:bg-gray-300 dark:hover:bg-gray-700 flex py-2 items-center rounded-lg px-3 -mx-3 -my-[1px]">
                    <div class="flex flex-col gap-y-1">
                        <span class="dark:text-white text-xs duration-500">{{ $section->name }}</span>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
</aside>
</div>