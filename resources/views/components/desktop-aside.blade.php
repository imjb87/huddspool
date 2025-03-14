<aside class="w-48 py-4 h-full hidden md:block">
    <h2 class="dark:text-white text-[10px] py-2 px-3 font-semibold -mx-3 duration-500 uppercase tracking-widest">Sections</h2>
    @foreach( $season->sections as $section )
        <a href="" class="block dark:text-white text-xs py-2 px-3 hover:bg-gray-300 dark:hover:bg-gray-800 -mx-3 rounded-lg duration-500">
            {{ $section->name }}
        </a>
    @endforeach
</aside>