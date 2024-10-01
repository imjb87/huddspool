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
                        <h3 class="text-sm font-semibold leading-6 text-white">Standings</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            @foreach ($rulesets as $ruleset)
                                <li>
                                    <a href="{{ route('standings.index', $ruleset->id) }}"
                                        class="text-sm leading-6 text-gray-300 hover:text-white">{{ $ruleset->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-10 md:mt-0">
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
                    <div class="mt-10 md:mt-0">
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
                <div class="md:grid md:grid-cols-2 md:gap-8 col-span-2 md:col-span-1">
                    <div class="mt-10 md:mt-0">
                        <h3 class="text-sm font-semibold leading-6 text-white">Knockouts</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            <li>
                                <a href="{{ asset('knockouts/KOSCHEDULE.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">Knockout Schedule</a>
                            </li>

                            <li>
                                <a href="{{ asset('knockouts/BBINTSinglesKO.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">BB/Int Singles Knockout</a>
                            </li>
                            <li>
                                <a href="{{ asset('knockouts/BBTeamKO.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">BB/Int Team Knockout</a>
                            </li>
                            <li>
                                <a href="{{ asset('knockouts/DoublesKO.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">Doubles Knockout</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold leading-6 text-white hidden md:block">&nbsp;</h3>
                        <ul role="list" class="mt-6 space-y-4">
                            <li>
                                <a href="{{ asset('knockouts/EPASinglesKO.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">EPA Singles Knockout</a>
                            </li>
                            <li>
                                <a href="{{ asset('knockouts/EPATeamKO.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">EPA Team Knockout</a>
                            </li>
                            <li>
                                <a href="{{ asset('knockouts/MixedDoublesKO.pdf') }}" target="_blank"
                                    class="text-sm leading-6 text-gray-300 hover:text-white">Mixed Doubles Knockout</a>
                            </li>
                        </ul>
                    </div>
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
