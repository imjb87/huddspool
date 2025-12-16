<footer class="bg-neutral-800" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    @if ( session()->has('previous_user') )
        <div class="fixed bottom-0 right-0 p-4">            
            <a href="{{ route('admin.switch-back') }}"
                class="inline-flex items-center justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">Switch back to {{ session('previous_user')->name }}</a>
        </div>
    @endif
    <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:px-8 lg:py-32">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <img class="w-28" src="{{ asset('images/logo.png') }}" alt="League Logo">
            <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white">Tables</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            @foreach ($rulesets as $ruleset)
                                <li>
                                    <a href="{{ route('table.index', $ruleset->id) }}"
                                        class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white">Fixtures &amp; Results</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            @foreach ($rulesets as $ruleset)
                                <li>
                                    <a href="{{ route('fixture.index', $ruleset->id) }}"
                                        class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white">Averages</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            @foreach ($rulesets as $ruleset)
                                <li>
                                    <a href="{{ route('player.index', $ruleset->id) }}"
                                        class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white">Official</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            @foreach ($rulesets as $ruleset)
                                <li>
                                    <a href="{{ route('ruleset.show', $ruleset->id) }}"
                                        class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ route('page.show', 'handbook') }}"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">Handbook</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold leading-6 text-white">Knockouts</h3>
                    <ul role="list" class="mt-6 grid grid-cols-2 grid-rows-{{ ceil($active_knockouts->count() / 2) }} gap-4 items-start">
                        @forelse ($active_knockouts as $knockout)
                            <li>
                                <a href="{{ route('knockout.show', $knockout) }}"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">
                                    {{ $knockout->name }}
                                </a>
                            </li>
                        @empty
                            <li class="text-sm leading-6 text-gray-400">No active knockouts right now.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-8 border-t border-white/10 pt-8 md:flex md:items-center md:justify-between">
            <p class="mt-8 text-xs leading-5 text-gray-400 md:order-1 md:mt-0">&copy; 2023 Huddersfield & District
                Tuesday Night Pool League.</p>
            <p class="mt-8 text-xs leading-5 text-gray-400 md:order-3 md:mt-0">Website by <a class="underline"
                    href="mailto:john@thebiggerboat.co.uk">John Bell</a></p>
        </div>
    </div>
</footer>
