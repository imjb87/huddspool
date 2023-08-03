<div>
@if($isOpen)
<div class="relative z-[99]" role="dialog" aria-modal="true" x-data="{ open: @entangle('isOpen') }">
    <!--
      Background backdrop, show/hide based on modal state.
  
      Entering: "ease-out duration-300"
        From: "opacity-0"
        To: "opacity-100"
      Leaving: "ease-in duration-200"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <!--
        Command palette, show/hide based on modal state.
  
        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
        <div @click.outside="open = false"
            class="mx-auto max-w-xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clip-rule="evenodd" />
                </svg>
                <input type="text" wire:model="searchTerm" wire:keyup="search"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                    placeholder="Search..." role="combobox" aria-expanded="false" aria-controls="options">
            </div>

            <!-- Default state, show/hide based on command palette state -->
            @if (empty($searchTerm))
                <div class="border-t border-gray-100 px-6 py-14 text-center text-sm sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.115 5.19l.319 1.913A6 6 0 008.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 002.288-4.042 1.087 1.087 0 00-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 01-.98-.314l-.295-.295a1.125 1.125 0 010-1.591l.13-.132a1.125 1.125 0 011.3-.21l.603.302a.809.809 0 001.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 001.528-1.732l.146-.292M6.115 5.19A9 9 0 1017.18 4.64M6.115 5.19A8.965 8.965 0 0112 3c1.929 0 3.716.607 5.18 1.64" />
                    </svg>
                    <p class="mt-4 font-semibold text-gray-900">Search for players, teams and venues</p>
                    <p class="mt-2 text-gray-500">Quickly find what you’re looking for by running a global search.</p>
                </div>
            @else
                <!-- Results, show/hide based on command palette state -->
                @if (!empty($searchResults))
                    <ul class="max-h-80 scroll-pb-2 scroll-pt-11 space-y-2 overflow-y-auto pb-2" id="options"
                        role="listbox">
                        @foreach( $searchResults as $name => $items )
                            @if ( count($items) > 0)
                                <li>
                                    <h2 class="bg-gray-100 px-4 py-2.5 text-xs font-semibold text-gray-900 uppercase">{{ $name }}</h2>
                                    <div class="mt-2 text-sm text-gray-800">
                                        @foreach ( $items as $item )
                                            <a class="flex justify-between px-4 py-2 whitespace-nowrap truncate" href="{{ route(Str::singular($name).'.show', $item->id) }}">
                                                <span>{{ $item->name }}</span>
                                                @if ($name == "players")
                                                    <span class="text-xs text-gray-500">{{ $item->team?->name }}</span>
                                                @endif
                                                @if ($name == "teams")
                                                    <span class="text-xs text-gray-500">{{ $item->section?->name }}</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <!-- Empty state, show/hide based on command palette state -->
                    <div class="border-t border-gray-100 px-6 py-14 text-center text-sm sm:px-14">
                        <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                        </svg>
                        <p class="mt-4 font-semibold text-gray-900">No results found</p>
                        <p class="mt-2 text-gray-500">We couldn’t find anything with that term. Please try again.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endif
</div>
