<header>
    <div class="bg-gray-900 hidden md:block">
        <div class="max-w-6xl mx-auto px-4 md:px-6">
            <h1 class="text-white text-xs py-2">Huddersfield & District Tuesday Night Pool League</h1>
        </div>
    </div>
    <div class="bg-gray-800 py-3 md:py-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between px-4 md:px-6">
                <x-application-logo />
                <div class="flex gap-x-2">
                    <button class="inline-flex items-center justify-center rounded-md p-2.5 text-gray-400 rounded-lg bg-gray-700 hover:bg-gray-900 duration-500 shadow"
                        id="searchIcon">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                    <button id="theme-toggle" type="button" class="text-gray-400 bg-gray-700 hover:bg-gray-900 dark:hover:bg-gray-900 focus:outline-none rounded-lg text-sm p-2.5 hidden md:inline duration-500 shadow">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    @if (Auth::check())
                    <a href="{{ route('player.show', Auth::user()) }}" class="inline-flex items-center justify-center rounded-lg p-2.5 bg-gray-700 hover:bg-gray-900 whitespace-nowrap text-xs text-gray-400 duration-500 shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="ml-1 text-white hidden md:inline">{{ Auth::user()->name }}</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md p-2.5 bg-gray-700 hover:bg-gray-900 whitespace-nowrap text-xs text-gray-400 duration-500 shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="ml-1 text-white hidden md:inline">Login</span>
                    </a>
                    @endif
                    <button class="inline-flex items-center justify-center rounded-md p-2.5 bg-gray-700 hover:bg-gray-900 md:hidden text-gray-400 duration-500 shadow" id="menuIcon">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('#searchIcon').forEach(item => {
            item.addEventListener('click', event => {
                Livewire.dispatch('openSearch'); // Emit the event to the Livewire component
                setTimeout(function() {
                    document.getElementById('searchInput').focus();
                }, 300);
            })
        })
    </script>
</header>