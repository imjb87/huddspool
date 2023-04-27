<header class="bg-neutral-800 shadow-lg">
    <nav class="mx-auto max-w-7xl px-6 lg:px-8" aria-label="Top">
        <div class="flex w-full items-center justify-between border-b border-slate-500 py-4 lg:border-none">
          <x-application-logo />
          <div class="hidden space-x-8 lg:block mx-auto">
            <a href="#" class="text-sm font-medium text-white hover:text-indigo-50">Tables</a>
  
            <a href="#" class="text-sm font-medium text-white hover:text-indigo-50">Fixtures</a>
  
            <a href="#" class="text-sm font-medium text-white hover:text-indigo-50">Results</a>
  
            <a href="#" class="text-sm font-medium text-white hover:text-indigo-50">Venues</a>
          </div>
          <div class="space-x-4">
            <a class="inline-block rounded-md border border-transparent text-sm font-medium hover:bg-opacity-75" href="{{ route('dashboard') }}">
              <i class="fas fa-user-circle fa-2x text-green-700"></i>
            </a>
          </div>
        </div>
    </nav>
</header>
  