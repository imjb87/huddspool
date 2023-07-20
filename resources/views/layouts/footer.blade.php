<footer class="bg-neutral-800" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:px-8 lg:py-32">
      <div class="xl:grid xl:grid-cols-3 xl:gap-8">
        <img class="w-28" src="{{ asset('images/logo.png') }}" alt="League Logo">
        <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm font-semibold leading-6 text-white">Tables</h3>
              <ul role="list" class="mt-6 space-y-4">
                @foreach($rulesets as $ruleset)                
                <li>
                  <a href="{{ route('table.index', $ruleset->id) }}" class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                </li>
                @endforeach
              </ul>
            </div>
            <div class="mt-10 md:mt-0">
              <h3 class="text-sm font-semibold leading-6 text-white">Fixtures &amp; Results</h3>
              <ul role="list" class="mt-6 space-y-4">
                @foreach($rulesets as $ruleset)                
                <li>
                  <a href="{{ route('fixture.index', $ruleset->id) }}" class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                </li>
                @endforeach
              </ul>
            </div>
            <div class="mt-10 md:mt-0">
              <h3 class="text-sm font-semibold leading-6 text-white">Other</h3>
              <ul role="list" class="mt-6 space-y-4">
                <li>
                  <a href="{{ route('venue.index') }}" class="text-sm leading-6 text-gray-300 hover:text-white">Venues</a>
                </li>
              </ul>
            </div>
          </div>
          <div class="md:grid md:grid-cols-2 md:gap-8">
            <div>
              <h3 class="text-sm font-semibold leading-6 text-white">Averages</h3>
              <ul role="list" class="mt-6 space-y-4">
                @foreach($rulesets as $ruleset)                
                <li>
                  <a href="{{ route('player.index', $ruleset->id) }}" class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                </li>
                @endforeach
              </ul>
            </div>
            <div class="mt-10 md:mt-0">
              <h3 class="text-sm font-semibold leading-6 text-white">Official</h3>
              <ul role="list" class="mt-6 space-y-4">
                @foreach($rulesets as $ruleset)                
                <li>
                  <a href="{{ route('ruleset.show', $ruleset->id) }}" class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-8 border-t border-white/10 pt-8 md:flex md:items-center md:justify-between">
        <p class="mt-8 text-xs leading-5 text-gray-400 md:order-1 md:mt-0">&copy; 2023 Huddersfield & District Tuesday Night Pool League.</p>
        <p class="mt-8 text-xs leading-5 text-gray-400 md:order-3 md:mt-0">Website by <a class="underline" href="mailto:john@thebiggerboat.co.uk">John Bell</a></p>
      </div>      
    </div>
  </footer>
  